import sys
sys.path.insert(0,"/var/www/html/webkmeans/server/api/.venv/lib/python3.11/site-packages")
import pandas as pd
import warnings
warnings.filterwarnings('ignore')
import os 
os.environ['MPLCONFIGDIR'] = os.getcwd() + "/configs/"
import matplotlib.pyplot as plt
from pandas.plotting import parallel_coordinates


if (sys.argv[4]=="csv"):
    df=pd.read_csv(sys.argv[1])
else:
    df=pd.read_excel(sys.argv[1])


columns=sys.argv[2].split(',')

clusters=int(sys.argv[3])

columns.append('cluster');
parallel_coordinates(df[columns],'cluster')

fig = plt.gcf()
if(len(columns)<=20):
    fig.set_size_inches((9, 5), forward=False)    
if(len(columns)<=30):
    fig.set_size_inches((11, 6), forward=False)      
if(len(columns)>30):
    fig.set_size_inches((15,7), forward=False)

fig.savefig(sys.argv[5], dpi=500,bbox_inches='tight')

