% AssignmentMM4b
clc; clear all; close all;
% Dimensioning

Nodes = 12;
rng(2);
% Degree 3 cyclic topology with 12 nodes
pathsetCR = [[1:Nodes]', [2:Nodes, 1]';[1:2:Nodes]', [mod((0:2:Nodes-1)+3,Nodes)+1]'];
pathsetCR = [pathsetCR, ones(length(pathsetCR),1)];
Network = graph(pathsetCR(:,1),pathsetCR(:,2),pathsetCR(:,3));
figure
NetworkFig = plot(Network,'layout','force3');
hold on
title('CR as 3D layout')
DistNetwork = distances(Network);

%% Traffic Matrix
TrafficMatrix = randi([5 20],12); % Random select between 5 and 20
TrafficMatrix = TrafficMatrix - diag(diag(TrafficMatrix)); % Remove diagonal

%% Primary paths from all-to-all
PrimaryPath = cell(Nodes); % Create path cell array
for i = 1:Nodes
    firstNode = i;
    for j = 1:Nodes
        secondNode = j;
        PrimaryPath{secondNode,firstNode} = shortestpath(Network,secondNode,firstNode);
    end
end

%% Load Matrix
LoadMatrix = zeros(Nodes);
for i = 1:Nodes
    firstNode = i;
    for j = 1:Nodes
        secondNode = j;
        CurrentPath = PrimaryPath{firstNode,secondNode};
        CurrentLoad = TrafficMatrix(firstNode,secondNode);
        for k = 2:length(CurrentPath)
            LoadMatrix(CurrentPath(k-1),CurrentPath(k)) = ...
                LoadMatrix(CurrentPath(k-1),CurrentPath(k)) + CurrentLoad;
        end
    end
end

%% Capacity Matrix
CapacityMatrix = ceil(LoadMatrix./16); % load

