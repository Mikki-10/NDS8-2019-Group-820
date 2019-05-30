% AssignmentMM4b
clc; clear all; close all;

%item = ['A' 'B' 'C' 'D' 'E' 'F' 'G' 'H' 'I' 'J' 'K' 'L'];
item = [1 2 3 4 5 6 7 8 9 10 11 12];
price = [60 20 49 57 49 35 51 61 44 64 81 46];
weight = [438 544 537 315 719 164 574 884 161 767 889 318];


n = 500;
maxn = 5;
maxp = 250;

Si = zeros(1,maxn);
Sp = zeros(1,maxn);
Sw = zeros(1,maxn);
S = zeros(n,2);
iteration = 0;
i = 1;
x = [];

while i < n+1
    Si = randsample(item,maxn);
    for j = 1:maxn
        Sp(j) = (price(Si(j)));
        Sw(j) = (weight(Si(j)));
        SumSp = sum(Sp);
        SumSw = sum(Sw);
    end
    if SumSp < 250
        S(i) = SumSp;
        S(i,2) = SumSw;
        i = i + 1;
        iteration = iteration + 1;
    else
        iteration = iteration + 1;
    end
    if i == n+1
        B = sortrows(S,-2);
        x = [B(1,1) B(1,2)];
        disp('      Max Price   Max Weight')
        disp(x)
    end
end
