% Miniproject 1 Henrik - Netwofork Flow Problem
% Suggest a network flow problem for which the flow deviation method is meaningfully illustrated 
% Write a program implementing the flow ddeviation method for the suggested
% network
% Initial route could be shortest paths (hop count)
clc; close all; clear all    

% We use the same network structure as Assignment3
Nodes = 12;
rng(2);
% Degree 3 cyclic topology with 12 Nodes
pathsetDSR = [[1:Nodes]', [2:Nodes, 1]';[Nodes+1:2*Nodes]', ...
[Nodes+2:2*Nodes, Nodes+1]'; [1:Nodes]',[Nodes+1:Nodes*2]' ];
pathsetDSR = [pathsetDSR, ones(length(pathsetDSR),1)];
Network = graph(pathsetDSR(:,1),pathsetDSR(:,2),pathsetDSR(:,3));
figure
NetworkFig = plot(Network,'layout','force3');
title('DSR as 3D layout')
DistNetwork = distances(Network);

% First we create the adjacency matrix, D, which shows which nodes has
% direct connection.
D=zeros(Nodes*2);% adjaceny
for n=1:length(pathsetDSR)
    D(pathsetDSR(n,1),pathsetDSR(n,2))=1;
    D(pathsetDSR(n,2),pathsetDSR(n,1))=1;
end
% We then define the capacity matrix, C, in relative terms, so 100%
% capacity is 1. It defines how much capacity each link/edge has.
% In the case of capacity one, the capacity matrix equals the adjacency
% matrix.
C = D;

% Define the Traffic matrix for traffic between two nodes
T = zeros(Nodes*2);
Lambda(6,24) = 0.95; % traffic from node 6  to node 24. It is the mean packets
% 6 -> 24 is furthest apart

% Define packet size and Service Rate
% Mu = C/P
P = 1;
Mu = C./P;

% Define average delay, T, for every link, as it is used as the weight for
% the shortest path algorithm. T = 1/Mu-lambda
T = 1./(Mu - Lambda(6,24));
T(T<=0) = 0; % If the links are negative then the capacity is exceeded.

% The T matrix can now be used as adjacency and weight matrix when doing
% the shortest path algorithm.
G_element = graph(T)
Graph_Element = plot(G_element, 'EdgeLabel', G_element.Edges.Weight,'layout','force3');

[path,d] = shortestpath(G_element,6,24) % shortest path and delay
highlight(Graph_Element,path,'EdgeColor','b','LineWidth',3)

% Using the shortest path gives us the optimal route which we can use to
% create the load matrix lambda.
% We do this by inserting traffic on the path link from source to
% destination
lambda = zeros(Nodes*2); % Load matrix
for n = 1:length(path)-1
    lambda(path(n),path(n+1)) = Lambda(6,24);
end

% We can then take 20% of the load out and re-route it through another
% route
for n = 1:length(path)-1
    lambda(path(n),path(n+1)) = Lambda(6,24)*0.8;
end

% We then remove the capacity that was already used
C = C - lambda;

% We define how much traffic is it we are routing and what is the service
% rate for the network
Lambda(6,24) = Lambda(6,24)*0.2;
Mu = C./P;

% To re-route we use the flow deviation method, where we have made a
% approximation of the delay, and then it is found that the weight cost for
% each link can be approximated as 
% ai=1/Lambda*Mui / (Mui - lambdai)^2
% Lambda is the resources to route
% Mui is service rate for link i
% lambdai is the load for link i
for n = 1:Nodes*2
    for k = 1:Nodes*2
        A(n,k) = 1/(Lambda(6,24)*Mu(n,k))/((Mu(n,k)-lambda(n,k))^2);
    end
end

% We can then use the shortest path on A
G_element = digraph(A);
[path2,d] = shortestpath(G_element,6,24)
highlight(Graph_Element,path2,'EdgeColor','g','LineWidth',3)

% the delay could be lowered further by using the right way
% T =(lambdai/(Mui - lambdai)) / Lambda

