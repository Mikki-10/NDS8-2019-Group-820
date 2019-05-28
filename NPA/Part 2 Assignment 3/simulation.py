import math
import numpy as np
import matplotlib.pyplot as plt
import random as rd

files_number = [823090,88018,58091,36081,23516,14434,11136,7550,5206,3062,1476,1778]

files_size = [1,16,32,64,128,256,512,1000,2000,4000,8000,16000]

log_fn=[]
log_fs=[]

for i in range (len(files_number)):
    log_fs.append(math.log(files_number[i]))
    log_fn.append(math.log(files_size[i]))
    
alpha = -np.polyfit(log_fs,log_fn,1)[1]

plt.plot(log_fs,log_fn)
plt.show()

def mg_simulation (alpha, xm = 0.1):
    limit_t=100
    t=0
    queue=[]
    l=10
    next_ser=10000
    p=rd.random()
    next_arr=t+(-math.log(1-p)/l)
    while t<limit_t:
        if t>=next_arr:
            p=rd.random()
            next_arr=t+(-math.log(1-p)/l)
            queue=[1]+queue
            if len(queue)==1:
                p=rd.random()
                next_ser=t+(xm/(1-p)**alpha)
        if t>=next_ser:
            queue.pop()
            if len(queue)!=0:
                p=rd.random()
                next_ser=t+(xm/(1-p)**alpha)
            else:
                next_ser=100000
        t+=1/100
        print(len(queue))
    print("La longueur de la queue en fin de simulation est de "+str((len(queue))))
            
        
        
#mg_simulation(alpha)       
    
def compute_L (l,mu,V):
    ro=l/mu
    return ro+(ro**2+(l**2)*V)/(2*(1-ro))
    

def mm_simulation (alpha, xm = 0.1):
    limit_t=100
    t=0
    queue=[]
    l=1000
    next_ser=10000
    p=rd.random()
    next_arr=t+(-math.log(1-p)/l)
    while t<limit_t:
        if t>=next_arr:
            p=rd.random()
            next_arr=t+(-math.log(1-p)/l)
            queue=[1]+queue
            if len(queue)==1:
                p=rd.random()
                next_ser=t+(-math.log(1-p)/alpha)
        if t>=next_ser:
            queue.pop()
            if len(queue)!=0:
                p=rd.random()
                next_ser=t+(-math.log(1-p)/alpha)
            else:
                next_ser=100000
        t+=1/10000
        print(next_arr)
        print(len(queue))
    print("La longueur de la queue en fin de simulation est de "+str((len(queue))))
    
mm_simulation(alpha)
