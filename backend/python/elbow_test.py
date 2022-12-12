from sklearn.cluster import KMeans
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import sys
from kneed import KneeLocator
import matplotlib.pyplot as plt


# print(os.getcwd())

df=pd.read_csv("backend/python/fuel_prices_52.csv")

columns=["unleaded95","unleaded100"]


scaler=MinMaxScaler()


for i in range(len(columns)):
    scaler.fit(df[[columns[i]]])
    df[columns[i]]=scaler.transform(df[[columns[i]]])


sse = []

for i in range(1,10):
    kmeans = KMeans(n_clusters=i)
    kmeans.fit(df[columns])
    sse.append(kmeans.inertia_)

kl=KneeLocator(range(1,10),sse,curve="convex",direction="decreasing")
print(kl.elbow)

print(sse)

plt.plot(range(1,10), sse, 'bx-')
plt.xlabel('Values of K')
plt.ylabel('Distortion')
plt.title('The Elbow Method using Distortion')
plt.show()