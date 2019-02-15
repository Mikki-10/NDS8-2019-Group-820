INSERT INTO public.nodes
	(x, y, customers)
VALUES 
	(floor(random() * 100)::int, 
		floor(random() * 100)::int, 
		floor(random() * 10 + 1)::int)