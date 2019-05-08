import numpy as np
from scipy.linalg import expm
import matplotlib.pyplot as plt

plt.close('all')

#those are the rates at which components fail (in minutes)

jmphost_d=0.002
ldap_d=0.002
vpn_d=0.002
cs_d=0.001
repair=0.05

#State 0: nothing down
#1: vpn down
#2: vpn+ldap
#3: vpn+jmphost
#4: vpn+cs
#5: vpn+ldap+jmphost
#6: vpn+ldap+cs
#7: vpn+jmphost+cs
#8: vpn+jmphost+cs+ldap
#9: ldap
#10: ldap+jmphost
#11: ldap+cs
#12: ldap+jumphost+cs
#13: jumphost
#14: jumphost+cs
#15: cs

#Q is the transition matrix corresponding to our markov chain

Q=np.array([[-(vpn_d+ldap_d+jmphost_d+cs_d),vpn_d,0,0,0,0,0,0,0,ldap_d,0,0,0,jmphost_d,0,cs_d],[repair,-(repair+ldap_d+2*jmphost_d+cs_d),ldap_d,2*jmphost_d,cs_d,0,0,0,0,0,0,0,0,0,0,0],[0,repair,-(2*repair+4*jmphost_d+cs_d),0,0,2*jmphost_d,cs_d,0,0,repair,0,0,0,0,0,0],[0,repair,0,-(2*repair+ldap_d+2*cs_d),0,ldap_d,0,2*cs_d,0,0,0,0,0,repair,0,0],[0,repair,0,0,-(2*repair+ldap_d+2*jmphost_d),0,ldap_d,2*jmphost_d,0,0,0,0,0,0,0,repair],[0,0,repair,repair,0,-(3*repair+2*cs_d),0,0,2*cs_d,0,repair,0,0,0,0,0],[0,0,repair,0,repair,0,-(3*repair+4*jmphost_d),0,4*jmphost_d,0,0,repair,0,0,0,0],[0,0,0,repair,repair,0,0,-(3*repair+ldap_d),ldap_d,0,0,0,0,0,repair,0],[0,0,0,0,0,repair,repair,repair,-4*repair,0,0,0,repair,0,0,0],[repair,0,vpn_d,0,0,0,0,0,0,-(repair+vpn_d+2*jmphost_d+cs_d),2*jmphost_d,cs_d,0,0,0,0],[0,0,0,0,0,vpn_d,0,0,0,repair,-(2*repair+2*cs_d+vpn_d),0,2*cs_d,repair,0,0],[0,0,0,0,0,0,vpn_d,0,0,repair,0,-(2*repair+2*jmphost_d+vpn_d),2*jmphost_d,0,0,repair],[0,0,0,0,0,0,0,0,vpn_d,0,repair,repair,-(3*repair+vpn_d),0,repair,0],[repair,0,0,vpn_d,0,0,0,0,0,0,ldap_d,0,0,-(repair+vpn_d+ldap_d+2*cs_d),2*cs_d,0],[0,0,0,0,0,0,vpn_d,0,0,0,0,0,ldap_d,repair,-(2*repair+vpn_d+ldap_d),repair],[repair,0,0,0,vpn_d,0,0,0,0,0,0,ldap_d,0,0,jmphost_d,-(repair+jmphost_d+vpn_d+ldap_d)]])

#this is the initial state vector at which we begin

Ini=np.array([1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0])

#A(t) gives the sum of the probabilities of being in the states in the right vector

def A(t):
    return np.dot(np.dot(Ini,expm(Q*t)),[0,0,0,0,1,0,1,1,1,0,0,1,1,0,1,1])
        
T=[i for i in range (0,1000,1)]
A=[A(t) for t in T]

#So those last lines will show the evolution of the probability of being in those states over time

plt.plot(T,A)
plt.show()
