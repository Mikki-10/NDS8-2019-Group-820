#for jumphost:
#1: Failure to connect
#2: No Response
#3: Wrong Data Added
#4: Cannot Read Data
#5: Cannot Add Data
#6: Overwriting Wrong Data
#7: Added Duplicate Data
matrice_transition=[[0.35,0.55,0.02,0.02,0.02,0.02,0.02],[0.12,0.35,0.22,0.02,0.02,0.25,0.02],[0.04,0.22,0.32,0.13,0.17,0.06,0.06],[0.05,0.05,0.05,0.4,0.4,0.03,0.02],[0.05,0.05,0.05,0.4,0.4,0.03,0.02],[0.05,0.20,0.05,0.05,0.05,0.30,0.30],[0.04,0.04,0.04,0.04,0.04,0.30,0.50]]

#1 System Failure
#2 Duplicate Data
#3 Loss of Data
proba_émission=[[0.94,0.03,0.03],[0.90,0.05,0.05],[0.07,0.07,0.84],[0.05,0.05,0.90],[0.12,0.09,0.79],[0.04,0.04,0.92],[0.09,0.82,0.09]]

#With forward backward, we can compute the most probable state at time t being given an observation sequence

def forward(observation) :
    M=[]
    for j in range(len(observation)):
        M.append([])
        for i in range (len(matrice_transition)):
            if j==0:
                M[j].append(proba_émission[i][observation[j]])
            else:
                S=0
                for k in range(len(Matrice[j-1])):
                    S+=M[j-1][k]*matrice_transition[k][i]*proba_émission[i][observation[j]]
                M[j].append(S)
    return M

def backward(observation) :
    M=[[]]
    for k in range(len(matrice_transition)):
        M[0].append(1)
    for j in range(len(observation)-1):
        M.append([])
        for i in range(len(matrice_transition)):
            S=0
            for k in range(len(matrice_transition)):
                S+=matrice_transition[k][i]*proba_émission[k][proba_émission[len(observation)-1-j]]*M[j-1][k]
            M[j].append(S)
    return M

#Viterbi is more suited for what we are going to do, because it computes the most probable sequence of states

def viterbi(observation):
    V=[[]]
    I=[[]]
    for i in range(len(matrice_transition)):
        V[0].append(proba_émission[i][observation[0]])
        I[0].append([i])
    for j in range(1,len(observation)):
        V.append([])
        I.append([])
        for i in range(len(matrice_transition)):
            m=-1
            index=-1
            for k in range(len(matrice_transition)):
                m=max(m,V[j-1][k]*proba_émission[i][observation[j]]*matrice_transition[k][i])
                if m == V[j-1][k]*proba_émission[i][observation[j]]*matrice_transition[k][i]:
                    index=k
            V[j].append(m)
            I[j].append(I[j-1][index]+[i])
    m=-1
    ind=-1
    for i in range(len(V[-1])):
        if V[-1][i]>m:
            m=V[-1][i]
            ind=i
    return I[-1][ind]

#we could also write Baulch Wellman, which would be useful to find the parameters of an HMM, being given observations and some other parameters, but since we cannot apply it in our case, I'd rather not write it.



print(viterbi([0,2,0,1]))
