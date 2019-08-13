% Miniproject 5 Henrik
clc; clear all; close all;

% Stats from scanning Tarik's PC
files_size = [16; 32; 64; 128; 256; 512; 1000; 2000; 4000; 8000; 16000; 32000];
pct = [79; 10.7; 4.6; 2.4; 1.2; 0.8; 0.5; 0.3; 0.3; 0.1; 0.1; 0.1];
files_number = [489261; 66107; 28368; 14622; 7317; 4997; 3234; 2120; 1610; 720; 397; 680];

% Plot stats
log_fn = [];
log_fs = [];

for i=1:length(files_number)
    log_fn(i) = log(files_number(i));
    log_fs(i) = log(files_size(i));
end

% Check slope (power tail)
polyfit = polyfit(log_fs,log_fn, 1);
alpha = abs(polyfit(1));

figure(1)
plot(log_fs,log_fn)
xlabel('Sizes (log)'); ylabel('Files (log)');

% Hyper Exponential

