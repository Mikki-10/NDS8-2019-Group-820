require 'pg'
require 'pp'

def makeNode(conn, n)
	conn.transaction do |c|
		n.times do 
			c.exec('INSERT INTO public.nodes(
				x, y, customers)
				VALUES (floor(random() * 100)::int, floor(random() * 100)::int, floor(random() * 10 + 1)::int);')
		end
	end
end

def getNrandom(conn, n)
	# YES I KNOW THERE IS AN SQL INJECTION POSSIBILITY HERE >:(
	nodes = []
	conn.query("SELECT * FROM public.nodes ORDER BY Random() LIMIT #{n}") do |result|
		result.each do |row|
			nodes << row["nodeid"]
		end
	end
	return nodes
end

def getUnconnectedPair(conn)
	nodes = []
	conn.query("-- Matches up one already connected node, with one unconnected node.
				WITH connected_node AS (
					SELECT nodeid FROM connections
					--ORDER BY Random()
				), unconnected_node AS (
					SELECT nodeid FROM nodes
					WHERE nodeid NOT IN (SELECT nodeid FROM connections)
					--ORDER BY Random()
				)

				SELECT cn.nodeid as node1, ucn.nodeid as node2
				from connected_node as cn, unconnected_node as ucn
				ORDER BY Random()
				LIMIT 1;
				") do |result|
		result.each do |row|
			nodes = [row["node1"], row["node2"]]
			end
	end
	return nodes
end

def connect2Nodes(conn, id1, id2)
	conn.exec("INSERT INTO public.connections
				(nodeid, neighbour)
			   VALUES
			   	(#{id1},#{id2}),
			   	(#{id2},#{id1})")
end

def checkPairExistance(conn, id1, id2)
	count = 0

	conn.query("WITH node1 AS ( SELECT #{id1} as id
				), node2 AS (SELECT #{id2} as id2)

				SELECT count(*)
				FROM node1, node2, connections
				WHERE node1.id = connections.nodeid
					AND node2.id2 = connections.neighbour") do |result|

		result.each do |row|
			count = row["count"].to_i
		end

	end

	return true if count > 0
	return false

end

def truncate(conn)
	conn.exec('truncate connections')
	conn.exec('truncate nodes')
end

def getAllConnections(conn)
	connections = []

	conn.query("SELECT con.nodeid, nodecoord.X as nodex, nodecoord.Y as nodey, con.neighbour, neighcoord.X as neighx, neighcoord.Y as neighy
				FROM connections as con
				JOIN nodes nodecoord on nodecoord.nodeid = con.nodeid
				JOIN nodes neighcoord on neighcoord.nodeid = con.neighbour
				ORDER by con.nodeid") do |result|
		result.each do |row|
			connections << row
		end
		
	end
	return connections
end

def getAllNodes(conn)
	nodes = []

	conn.query("SELECT * FROM nodes;") do |result|
		result.each do |row|
			nodes << row
		end
	end
	return nodes
end

def setDist(conn, node, neighbour, dist)
	conn.exec(" UPDATE public.connections
				SET dist = #{dist}
				WHERE nodeid = #{node} AND neighbour = #{neighbour};")
end

def clacDist(conn, connections)
	connections.each do |c|
		dist = Math.sqrt((c["nodex"].to_f - c["neighx"].to_f)**2 + (c["nodey"].to_f - c["neighy"].to_f)**2)
		setDist(conn, c["nodeid"], c["neighbour"], dist)
	end
end

def makeStart(conn)
	conn.exec("UPDATE nodes
				SET customers = 0
				WHERE nodeid = (SELECT nodeid FROM nodes LIMIT 1)")

	return getStartNode(conn)
end

def getStartNode(conn)
	startNode = 0
	conn.query("SELECT nodeid FROM nodes WHERE customers = 0") do |result|
		result.each do |row|
			startNode = row["nodeid"]
		end
	end

	return startNode
end

def initDB(conn)
	truncate(conn)
	makeNode(conn, 20)

	# Make initial connection
	nodes = getNrandom(conn, 2)
	connect2Nodes(conn, nodes[0], nodes[1])

	# Ensure conectivity to all nodes
	18.times do |i|
		nodes = getUnconnectedPair(conn)
		puts "Unconnected pair: #{nodes}"
		connect2Nodes(conn, nodes[0], nodes[1])			
		puts "Added node nr #{i}"
	end

	# Connect the rest
	31.times do |i|
		nodes = getNrandom(conn, 2)
		while checkPairExistance(conn, nodes[0], nodes[1])
			nodes = getNrandom(conn, 2)
		end
		connect2Nodes(conn, nodes[0], nodes[1])
		puts "Added remaining node #{i}"
	end

	connections = getAllConnections(conn)
	clacDist(conn, connections)

	return makeStart(conn)
end

def getNeighbours(conn, node)
	neighbours = []
	conn.query("SELECT neighbour, dist
				FROM connections
				WHERE nodeid = #{node}
				ORDER BY dist asc") do |result|
		result.each do |row|
			neighbours << row
		end
	end
	return neighbours
end

def main()
	conn = PG.connect( dbname: 'drblah' )

	#startNode = initDB(conn)
	startNode = getStartNode(conn)

	puts "Startnode is: #{startNode}"
	#graph = {}

	#graph[startNode] = {:dist => 0}

	#neighbours = getNeighbours(conn, startNode)

	#graph[neighbours[0]["neighbour"]] = {:dist => neighbours[0]["dist"]}

	dist = {}
	q = []
	prev = {}

	dist[startNode] = 0

	getAllNodes(conn).each do |v|
		if v["nodeid"] != startNode
			dist[v["nodeid"]] = 10**6
		end
		prev[v["nodeid"]] = nil

		q.push( {:nodeid => v["nodeid"], :dist => dist[v["nodeid"]]} )

	end

	while not q.empty?
		q.sort_by! { |node| node[:dist] }
		q.reverse!

		u = q.pop

		getNeighbours(conn, u[:nodeid]).each do |n|
			alt = u[:dist] + n["dist"].to_f
			if alt < dist[n["neighbour"]]
				dist[n["neighbour"]] = alt
				prev[n["neighbour"]] = u

				q.each do |vertex|
					if vertex[:nodeid] == n["neighbour"]
						vertex[:dist] = alt
					end
				end
			end
		end

	end
	puts "----"
	pp dist
	puts "----"
	pp prev


end

main()