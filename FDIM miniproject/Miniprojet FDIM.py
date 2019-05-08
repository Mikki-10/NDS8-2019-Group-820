import numpy as np
from scipy.linalg import expm
import matplotlib.pyplot as plt

jmphost_d=0.002
lpad_d=0.002
vpn_d=0.002
cs_d=0.001
repair=0.005

#State 0: nothing down
#1: vpn down
#2: vpn+lpad
#3: vpn+jmphost
#4: vpn+cs
#5: vpn+lpad+jmphost
#6: vpn+lpad+cs
#7: vpn+jmphost+cs
#8: vpn+jmphost+cs+lpad
#9: lpad
#10: lpad+jmphost
#11: lpad+cs
#12: lpad+jumphost+cs
#13: jumphost
#14: jumphost+cs
#15: cs

Q=np.array([[-(vpn_d+lpad_d+jmphost_d+cs_d),vpn_d,0,0,0,0,0,0,0,lpad_d,0,0,0,jmphost_d,0,cs_d],[repair,-(repair+lpad_d+2*jmphost_d+cs_d),lpad_d,2*jmphost_d,cs_d,0,0,0,0,0,0,0,0,0,0,0],[0,repair,-(2*repair+4*jmphost_d+cs_d),0,0,2*jmphost_d,cs_d,0,0,repair,0,0,0,0,0,0],[0,repair,0,-(2*repair+lpad_d+2*cs_d),0,lpad_d,0,2*cs_d,0,0,0,0,0,repair,0,0],[0,repair,0,0,-(2*repair+lpad_d+2*jmphost_d),0,lpad_d,2*jmphost_d,0,0,0,0,0,0,0,repair],[0,0,repair,repair,0,-(3*repair+2*cs_d),0,0,2*cs_d,0,repair,0,0,0,0,0],[0,0,repair,0,repair,0,-(3*repair+4*jmphost_d),0,4*jmphost_d,0,0,repair,0,0,0,0],[0,0,0,repair,repair,0,0,-(3*repair+lpad_d),lpad_d,0,0,0,0,0,repair,0],[0,0,0,0,0,repair,repair,repair,-4*repair,0,0,0,repair,0,0,0],[repair,0,vpn_d,0,0,0,0,0,0,-(repair+vpn_d+2*jmphost_d+cs_d),2*jmphost_d,cs_d,0,0,0,0],[0,0,0,0,0,vpn_d,0,0,0,repair,-(2*repair+2*cs_d+vpn_d),0,2*cs_d,repair,0,0],[0,0,0,0,0,0,vpn_d,0,0,repair,0,-(2*repair+2*jmphost_d+vpn_d),2*jmphost_d,0,0,repair],[0,0,0,0,0,0,0,0,vpn_d,0,repair,repair,-(3*repair+vpn_d),0,repair,0],[repair,0,0,vpn_d,0,0,0,0,0,0,lpad_d,0,0,-(repair+vpn_d+lpad_d+2*cs_d),2*cs_d,0],[0,0,0,0,0,0,vpn_d,0,0,0,0,0,lpad_d,repair,-(2*repair+vpn_d+lpad_d),repair],[repair,0,0,0,vpn_d,0,0,0,0,0,0,lpad_d,0,0,jmphost_d,-(repair+jmphost_d+vpn_d+lpad_d)]])

Ini=np.array([1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0])

def A(t):
    return np.dot(np.dot(Ini,expm(Q*t)),[1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0])
    
print(A(1))    
    
T=[i for i in range (0,1000,1)]
A=[A(t) for t in T]

plt.plot(T,A)
plt.show()
