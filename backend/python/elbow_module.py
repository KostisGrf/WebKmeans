from sklearn.cluster import KMeans
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import sys


df=pd.read_csv(sys.argv[1])

columns=sys.argv[2].split(',')

scaler=MinMaxScaler()


for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])


sse = []

for i in range(1,12):
    kmeans = KMeans(n_clusters=i)
    kmeans.fit(df[columns])
    sse.append(kmeans.inertia_)


print(sse)


