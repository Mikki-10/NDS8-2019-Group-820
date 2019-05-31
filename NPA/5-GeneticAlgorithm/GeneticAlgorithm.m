function [solution, weight, price] = GeneticAlgorithm(Initpop,Items,Iterations,MaxSpend)
% Genetic Algorithm to compute the optimal shopping.
%   Input for the function is 
%       the initialised population
%       items available
%       iterations
%       max amount to spend
%
CurrentPop=Initpop;
for iter = 1:iterations
    % Find the best in our current gen
    % Get the probability of each
    % Find the parents set based on their probability
    % Mix the parents
        % Crossover
    % New population
    % Remove all illigal (based on maxspend)
    % Find the best chromosones of generation
    % Set currentpop to newpop with best chromosones
end

end