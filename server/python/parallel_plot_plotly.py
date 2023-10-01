import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import pandas as pd
import warnings
warnings.filterwarnings('ignore')
import numpy as np
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
import plotly.graph_objects as go
import plotly.io as pio
from pandas.plotting import parallel_coordinates


if (sys.argv[4]=="csv"):
    df1=pd.read_csv(sys.argv[1])
else:
    df1=pd.read_excel(sys.argv[1])

   


columns=sys.argv[2].split(',')
df=df1[columns] 

clusters=int(sys.argv[3])



numerical_columns = df.select_dtypes(include=np.number).columns.tolist()
categorical_columns = df.select_dtypes(include=['object']).columns.tolist()

dimensions = []

i=0

for col in df.columns:
    if col in numerical_columns:
        dimensions.append(dict(label=col, values=df[col]))
    else:
        i+=1
        group_vars = df[col].unique()
        dfg = pd.DataFrame({col:df[col].unique()})
        dfg[f'dummy{i}'] = dfg.index
        df = pd.merge(df, dfg, on = col, how='left')
        dimensions.append(dict(range=[0,df[f'dummy{i}'].max()],
                       tickvals = dfg[f'dummy{i}'], ticktext = dfg[col],
                       label=col, values=df[f'dummy{i}']),)
        
tickvals=df1['cluster'].unique()  
ticktext=df1['cluster'].unique().astype(str) 



fig = go.Figure(data=go.Parcoords(
    line=dict(color=df1['cluster'],  # Using index for line coloring
              colorscale=[[0, 'rgba(200,0,0,0.1)'],
                          [0.5, 'rgba(0,200,0,0.1)'],
                          [1, 'rgba(0,0,200,0.1)']],
              showscale=True,
              colorbar=dict(tickvals=tickvals, ticktext=ticktext,title="cluster")
             ),
    dimensions=dimensions
))


pio.write_html(fig,sys.argv[5])

