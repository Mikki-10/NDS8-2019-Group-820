clc; clear all; close all;
% MM4 Availability
% MTTF 1 failure per 10^9 hours

% MTTF/MFT
LineMTTFFit = 500;
LineMFT = 14.4;

NodeMTTFFit = 2850;
NodeMFT = 2;

AmpMTTFFit = 502;
AmpMFT = 6;

%% Lines
% Top row
LineTop = [200 100 220 90];
LineTopMTTF = 10^9./(LineTop * LineMTTFFit);
ALineTop = LineTopMTTF./(LineTopMTTF + LineMFT)

% Bottom row
LineBottom = [300 80 180 400 250];
LineBottomMTTF = 10^9./(LineBottom * LineMTTFFit);
ALineBottom = LineBottomMTTF./(LineBottomMTTF + LineMFT)

%% Nodes
NodeMTTF = 10^9./NodeMTTFFit
ANode = NodeMTTF./(NodeMTTF + NodeMFT)

%%Amps
AmpMTTF = 10^9./AmpMTTFFit
AAmp = AmpMTTF./(AmpMTTF + AmpMFT)

%% Top row
AmpsTop = [2 1 2 1];
NodesTop = 3;
ATop = prod(ALineTop) * ANode^NodesTop * AAmp^(sum(AmpsTop))

%% Bottom row
AmpsBottom = [3 0 2 5 3];
NodesBottom = 4;
ABottom = prod(ALineBottom) * ANode^NodesBottom * AAmp^(sum(AmpsBottom))

%% Total
format long
ATotal = 1-((1-ATop)*(1-ABottom))
format short

