% Miniproject 2 Henrik - Netwofork Flow Problem for colouring schemes
% Suggest a network flow problem for which the colouring schemes may be
% meaningfully illustrated
% Write a program that
%   Find shortest path routes for s,d pairs
%   Construct aux graph
%   Estimate number of colours needed in degree order
%   Performs the coouring in degree order
%   Finds the alternate order
%   Performs colouring on alterate order7
clc; clear all; close all;

Nodes = 6
pathsetDSR = [1 2 1; 1 3 1; 2 3 1; 2 4 1; 3 5 1; 4 5 1; 4 6 1; 5 6 1]
network = graph(pathsetDSR(:,1),pathsetDSR(:,2),pathsetDSR(:,3));
plot(network)

% Adjacency matrix
D=zeros(Nodes*2);% adjaceny
for j=1:length(pathsetDSR)
    D(pathsetDSR(j,1),pathsetDSR(j,2))=1;
    D(pathsetDSR(j,2),pathsetDSR(j,1))=1;
end

% We calculate the shortest path for all possible links
shortestpathsettree{1}=shortestpath(network,1,6); % link 1
shortestpathsettree{2}=shortestpath(network,2,6); % link 2
shortestpathsettree{3}=shortestpath(network,4,6); % link 3
shortestpathsettree{4}=shortestpath(network,5,4); % link 4
shortestpathsettree{5}=shortestpath(network,5,1); % link 5
shortestpathsettree{6}=shortestpath(network,5,2); % link 6
shortestpathsettree{7}=shortestpath(network,4,3); % link 7

% We create a matrixx with all the edges we pass through
shortestpathset = cell(length(shortestpathsettree),1);
for i = 1:length(shortestpathsettree)
    shortestpathset{i}=[shortestpathsettree{i}(1:end-1)' shortestpathsettree{i}(2:end)'];
end

% Based on the  edges for each light path we have to find every lightpath
% which have any edges in common, if they have edges in common then we put
% a 1, this is the auxilary graph
auxiliaryGraphMatrix = zeros(length(shortestpathset));
for i = 1:length(shortestpathsettree)
    LinkUnderTest=[shortestpathset{i};flip(shortestpathset{i},2)]; % The link we have 
    for j = 1:length(shortestpathsettree)
        LinkTocompare=[shortestpathset{j};flip(shortestpathset{j},2)]; % The link we compare 
        if not(isempty(LinkUnderTest)) & not(isempty(LinkTocompare)) & not(i==j) % if 
            auxiliaryGraphMatrix(i,j)= sum(ismember(LinkUnderTest,LinkTocompare,'rows'));
        end
    end
end

% Based on the auxilary matrix we can make a auxilary graph
auxiliaryGraph=graph(auxiliaryGraphMatrix);
plot(auxiliaryGraph)

% We now find the chromatic number
% This is done by estimating the max uses of colours
Degree_Nodes = degree(auxiliaryGraph)
Degree_Graph = max(Degree_Nodes)
ChromaticNumber = Degree_Graph+1

% Now the colouring of each node by degree order. 
% We find the degree of the graph to find the chromatic number
nodeMatrix = [(1:length(auxiliaryGraphMatrix))',Degree_Nodes,zeros(length(auxiliaryGraphMatrix),1)];
nodeMatrix = sortrows(nodeMatrix,2,'descend')
Colours = 1:ChromaticNumber;

% We now give out the 4 available colours
% The nodes with the highest degree gets the colour first
% After that, all other nodes get a colour assigned.
Edges = auxiliaryGraph.Edges.EndNodes;
for i = 1:length(nodeMatrix)
    if i <= ChromaticNumber % Give highest degree node a colour
        nodeMatrix(i,3) = i; % 1 2 7 3 6 5 4
    end
    % Find all the edges for Node i
    idx = Edges(:,1) == nodeMatrix(i,1) | Edges(:,2) == nodeMatrix(i,1);
    % Find all the neighbouring nodes
    validNodes = unique([Edges(idx,1);Edges(idx,2)]);
    % Remove my self
    validNodes=validNodes(validNodes~=nodeMatrix(i,1));
    % Find the index for the nodes in the 'overall matrix'
    ActualPosOfNodes=ismember(nodeMatrix(:,1),validNodes);
    % Find the colors already used
    usedColours=nodeMatrix(ActualPosOfNodes,3);
    % Find which colors not used
    availableColoursidx=not(ismember(Colours,usedColours));
    % Getting the color 'value
    availableColours=Colours(availableColoursidx);
    % I just assign the first available color to the node
    nodeMatrix(i,3)=availableColours(1);
end
nodeMatrix

colourplot=plot(auxiliaryGraph);
ColoursNames=['r','b','m','g','k','c','y'];
for i = 1:length(nodeMatrix)
    highlight(colourplot,nodeMatrix(i,1),'NodeColor',ColoursNames(nodeMatrix(i,3)));
end

%% Alternate colouring problem
% In this problem we start at athe node with the lowest amount of edges and
% assign a colour to it, then you go to the second lowest and see what
% colours are available for that one and piick a new colour.

nodeMatrix = [(1:length(auxiliaryGraphMatrix))',Degree_Nodes,zeros(length(auxiliaryGraphMatrix),1)];
nodeMatrix = sortrows(nodeMatrix,2,'ascend')
Colours = 1:ChromaticNumber;

Edges = auxiliaryGraph.Edges.EndNodes;
for i = 1:length(nodeMatrix)
    % Find all the edges for Node i
    idx = Edges(:,1) == nodeMatrix(i,1) | Edges(:,2) == nodeMatrix(i,1);
    % Find all the neighbouring nodes
    validNodes = unique([Edges(idx,1);Edges(idx,2)]);
    % Remove my self
    validNodes=validNodes(validNodes~=nodeMatrix(i,1));
    % Find the index for the nodes in the 'overall matrix'
    ActualPosOfNodes=ismember(nodeMatrix(:,1),validNodes);
    % Find the colors already used
    usedColours=nodeMatrix(ActualPosOfNodes,3);
    % Find which colors not used
    availableColoursidx=not(ismember(Colours,usedColours));
    % Getting the color 'value
    availableColours=Colours(availableColoursidx);
    % I just assign the first available color to the node
    nodeMatrix(i,3)=availableColours(1);
end
nodeMatrix

% we colour the new graph
figure
colourplot=plot(auxiliaryGraph);
ColoursNames=['r','b','m','g','k','c','y'];
for i = 1:length(nodeMatrix)
    highlight(colourplot,nodeMatrix(i,1),'NodeColor',ColoursNames(nodeMatrix(i,3)));
end