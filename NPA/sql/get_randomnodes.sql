-- replace #{n} with the number of nodes you want
SELECT * FROM public.nodes ORDER BY Random() LIMIT #{n}