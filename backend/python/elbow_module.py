from sklearn.cluster import KMeans
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import sys
from kneed import KneeLocator
import json

if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1])
else:
    df=pd.read_excel(sys.argv[1])


columns=sys.argv[2].split(',')
clusters=int(sys.argv[3])

scaler=MinMaxScaler()


for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])


sse = []

for i in range(1,clusters):
    kmeans = KMeans(n_clusters=i,n_init='auto')
    kmeans.fit(df[columns])
    sse.append(kmeans.inertia_)



result=list(map(str, sse))
kl=KneeLocator(range(1,clusters),sse,curve="convex",direction="decreasing")

print(json.dumps({"sse":result,"suggested-k":str(kl.elbow)}))



