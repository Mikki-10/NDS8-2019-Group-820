function [MinimumPath,FiberLength,TrenchingLength] =  ShortestPathSpanningTree(ConnectionMatrix,UserPrHouseHold,AccessPoint)
% Bellman-ford algorithm
%   AccessPoint = 11
%   UserPrHouseHold = 20x1 double
%   ConnectionMatrix = 50x3 double (nodes)

Edges = [ConnectionMatrix;ConnectionMatrix(:,2:-1:1),ConnectionMatrix(:,3)];
distance(1:length(UserPrHouseHold))=Inf;
distance(AccessPoint)=0;
predecessor(1:length(UserPrHouseHold))=0;
for i = 1 : length(UserPrHouseHold) - 1
    for j = 1 : length(Edges)
        u = Edges(j,1);
        v = Edges(j,2);
        t = (distance(u) + Edges(j,3)); 
        if (t < distance(v) ) 
            distance(v) = t; 
            predecessor(v) = u; 
        end
    end
end

MinimumPath = [(1:length(UserPrHouseHold))', predecessor'];

MinimumPath=ConnectionMatrix(ismember(ConnectionMatrix(:,1:2),[MinimumPath; MinimumPath(:,2:-1:1)],'rows'),:);

TrenchingLength = sum(MinimumPath(:,3));

FiberLength=distance;
end

