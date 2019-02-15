CREATE TABLE public.nodes
(	-- IN other SQL DBs use autoincrement instead
    nodeid serial,
    x integer,
    y integer,
    customers integer,
    CONSTRAINT nodes_pkey PRIMARY KEY (nodeid)
)

CREATE TABLE public.connections
(
    nodeid integer,
    neighbour integer,
    dist double precision,
    CONSTRAINT connections_pkey PRIMARY KEY (nodeid, neighbour)

)