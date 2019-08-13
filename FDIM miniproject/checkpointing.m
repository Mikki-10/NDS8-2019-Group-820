clear all; close all;

T = 5;             % Task Duration
C = 0.2;            % Duration per checkpoint
mu = 0.1;           % Error rate
N = 1:20;           % Number of checkpoints
mdur = zeros(1,length(N)); % Mean duration

for i=N                                 % Loop N times
    i = i-1;                            % Start at index 0
    ps = exp(-mu*(T/((i+1)+C)));        % Success probability
    edur = (i*(T/(i+1)+C)+T/(i+1))/ps;  % Expected duration
    mdur(i+1) = edur;                   % Add to array
end

plot(N,mdur,'b-x','Linewidth',2);   % Plot durations 
xlabel('Number of Checkpoints N'); 
ylabel('Mean Duration of Task inc. CP and RB');
dim = [.2 .5 .3 .3];
str = {'Task duration at different','number of checkpoints.','T = 5','C = 0.2','mu = 0.1'};
annotation('textbox',dim,'String',str,'FitBoxToText','on');
grid on


%durtaskcheck = T/((N+1)+C)
%ps = exp(-mu*(T/((N+1)+C)));
%eduration = (N*(T/(N+1)+C)+T/(N+1))/ps;