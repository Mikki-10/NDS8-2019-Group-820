clc; clear all; close all;

% AHPD

N = 12;

% SR
s_single = [1 1 2 3 4 5 6 7 8 9 10 11];
t_single = [2 12 3 4 5 6 7 8 9 10 11 12];
G_single = graph(s_single,t_single);
plot(G_single)
grid on
d_single = distances(G_single);

averageHops = 0;
firstNode = 0;
secondNode = 0;

for i = 1:N
    firstNode = i;
    for j = 1:N
        secondNode = j;
        if firstNode ~= secondNode
            averageHops = averageHops + d_single(i,j);
        end
        if firstNode == 1 && secondNode == 12
            firstNodeHops = averageHops;
        end
    end
end

A_SR_n = (1/(N*(N-1))) * averageHops
D_SR_n = max(firstNodeHops)/(N/2)

A_SR_N_Formula = (N^2)/(4*(N-1))
D_SR_N_Formula = floor(N/2)


% DR
pathsetDSR = [[1:N]', [2:N, 1]';[N+1:2*N]', ...
[N+2:2*N, N+1]'; [1:N]',[N+1:N*2]' ];
pathsetDSR = [pathsetDSR, ones(length(pathsetDSR),1)];
figure
plot(graph(pathsetDSR(:,1),pathsetDSR(:,2),pathsetDSR(:,3)),'layout','force3')

A_DR_N_12Nodes_Formula = (((N^2)/4)+N)/(2*N-2)
D_DR_N_12Nodes_Formula = floor(N/4 + 1)

A_DR_N_24Nodes_Formula = ((((2*N)^2)/4)+(2*N))/(2*(2*N)-2)
D_DR_N_24Nodes_Formula = floor((2*N)/4 + 1)

% CR
pathsetCR = [[1:N]', [2:N, 1]';[1:2:N]', [mod((0:2:N-1)+3,N)+1]'];
pathsetCR = [pathsetCR, ones(length(pathsetCR),1)];
figure
plot(graph(pathsetCR(:,1),pathsetCR(:,2),pathsetCR(:,3)),'layout','circle')

G_CR = graph(pathsetCR(:,1),pathsetCR(:,2),pathsetCR(:,3));
figure
plot(G_CR)
grid on
d_cr = distances(G_CR);

averageHops = 0;
firstNode = 0;
secondNode = 0;

for i = 1:N
    firstNode = i;
    for j = 1:N
        secondNode = j;
        if firstNode ~= secondNode
            averageHops = averageHops + d_cr(i,j);
        end
        if firstNode == 1 && secondNode == 12
            firstNodeHops = averageHops;
        end
    end
end

A_CR_n = (1/(N*(N-1))) * averageHops
D_CR_n = max(firstNodeHops)/(N/2)
