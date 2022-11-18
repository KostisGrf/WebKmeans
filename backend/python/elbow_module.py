from sklearn.cluster import KMeans
import matplotlib.pyplot as plt
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import time
import sys
import json




df=pd.read_csv("SampleCSVFile_53000kb.csv")

columns=['Third','Fourth']

scaler=MinMaxScaler()
# columns=['unleaded95','nomos']

print(len(df))

for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])


sse = []

for i in range(1,12):
    kmeans = KMeans(n_clusters=i)
    kmeans.fit(df[columns])
    sse.append(kmeans.inertia_)


print(sse)

