import locale
import mysql.connector
import pandas as pd
import plotly.express as px
from flask import Flask, render_template_string
from dash import Dash, dcc, html as dash_html

locale.setlocale(locale.LC_TIME, 'es_ES.UTF-8')
hoy = pd.Timestamp.today()
mes_anio_actual = hoy.strftime("%B %Y").capitalize()
mes_anio_anterior = (hoy - pd.DateOffset(months=1)).strftime("%B %Y").capitalize()
año_actual = hoy.year
año_anterior = año_actual - 1

server = Flask(__name__)

def create_dashboard(server):
    dash_app = Dash(
        __name__, 
        server=server, 
        url_base_pathname='/dashboard/'
    )

    conn = mysql.connector.connect(
        host="localhost",
        port=3307,
        user="root",
        password="",
        database="marbellin_bd",
        charset="utf8mb4"
    )

    df1 = pd.read_sql(f"""
        SELECT YEAR(fecha_pedido) AS año,
               MONTH(fecha_pedido) AS mes,
               SUM(monto_total) AS total
        FROM pedidos
        WHERE fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY año, mes
        ORDER BY año, mes;
    """, conn).pivot(index='mes', columns='año', values='total').reset_index()

    df2 = pd.read_sql(f"""
        SELECT
            DATE_FORMAT(fecha_pedido, '%Y-%m') AS periodo,
            MONTH(fecha_pedido) AS mes,
            YEAR(fecha_pedido)  AS año,
            SUM(monto_total)    AS total
        FROM pedidos
        WHERE fecha_pedido >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
          AND fecha_pedido <= CURDATE()
        GROUP BY periodo, mes, año
        ORDER BY periodo;
    """, conn)

    df3 = pd.read_sql(f"""
        SELECT p.nombre        AS producto,
               SUM(pi.cantidad) AS unidades
        FROM pedido_items pi
        JOIN pedidos o ON pi.pedido_id = o.pedido_id
        JOIN productos p ON pi.producto_id = p.producto_id
        WHERE YEAR(o.fecha_pedido)  = {año_actual}
          AND MONTH(o.fecha_pedido) = {hoy.month}
        GROUP BY p.producto_id
        ORDER BY unidades DESC
        LIMIT 10;
    """, conn)

    df4 = pd.read_sql(f"""
        SELECT c.nombre                         AS categoria,
               SUM(pi.cantidad * pi.precio_unitario) AS ingresos
        FROM pedido_items pi
        JOIN pedidos o ON pi.pedido_id = o.pedido_id
        JOIN productos p ON pi.producto_id = p.producto_id
        JOIN categorias c ON p.categoria_id = c.categoria_id
        WHERE YEAR(o.fecha_pedido)  = {año_actual}
          AND MONTH(o.fecha_pedido) = {hoy.month}
        GROUP BY c.categoria_id
        ORDER BY ingresos DESC;
    """, conn)

    df5 = pd.read_sql(f"""
        SELECT d.nombre                  AS distrito,
               MONTH(o.fecha_pedido)     AS mes,
               SUM(o.monto_total)        AS total
        FROM pedidos o
        JOIN clientes cl ON o.cliente_id   = cl.cliente_id
        JOIN distritos d ON cl.distrito_id = d.distrito_id
        WHERE o.fecha_pedido >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
          AND o.fecha_pedido <= CURDATE()
        GROUP BY d.distrito_id, mes
        ORDER BY d.nombre, mes;
    """, conn)

    df6 = pd.read_sql(f"""
        WITH first_orders AS (
          SELECT cliente_id, MIN(fecha_pedido) AS first_order_date
          FROM pedidos
          GROUP BY cliente_id
        ), current_orders AS (
          SELECT DISTINCT o.cliente_id, f.first_order_date
          FROM pedidos o
          JOIN first_orders f USING(cliente_id)
          WHERE YEAR(o.fecha_pedido)  = {año_actual}
            AND MONTH(o.fecha_pedido) = {hoy.month}
        )
        SELECT CASE
                 WHEN YEAR(first_order_date)  = {año_actual}
                  AND MONTH(first_order_date) = {hoy.month}
                 THEN 'Nuevo'
                 ELSE 'Recurrente'
               END AS tipo_cliente,
               COUNT(*) AS cantidad
        FROM current_orders
        GROUP BY tipo_cliente;
    """, conn)

    conn.close()

    meses_es = {
        1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
        5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
        9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
    }

    fig1 = px.line(
        df1,
        x='mes',
        y=[año_anterior, año_actual],
        title=f'Ganancias Mensuales: {año_anterior} vs {año_actual}',
        labels={'mes':'Mes','value':'S/ Ingresos'}
    )
    fig1.update_traces(mode='lines+markers+text', textposition='top center')

    df2 = df2.sort_values('periodo')
    df2['mes_nombre'] = df2['mes'].map(meses_es)
    df2['etiqueta'] = df2['mes_nombre'] + ' ' + df2['año'].astype(str)
    fig2 = px.bar(
        df2,
        x='etiqueta',
        y='total',
        title=f'Ganancia del Mes: {mes_anio_anterior} vs {mes_anio_actual}',
        labels={'etiqueta':'Mes','total':'S/ Ingresos'},
        color_discrete_sequence=['#7ED957'],
        text='total'
    )
    fig2.update_traces(texttemplate='%{text:.2f}', textposition='outside')

    fig3 = px.bar(
        df3,
        x='unidades',
        y='producto',
        orientation='h',
        title=f'Top 10 Productos Más Vendidos ({mes_anio_actual})',
        labels={'unidades':'Unidades','producto':'Producto'},
        color_discrete_sequence=['#F266AB'],
        text='unidades'
    )
    fig3.update_traces(texttemplate='%{text}', textposition='outside')
    fig3.update_layout(yaxis={'categoryorder':'total ascending'})

    fig4 = px.pie(
        df4,
        names='categoria',
        values='ingresos',
        title=f'Ingresos por Categoría ({mes_anio_actual})'
    )
    fig4.update_traces(textinfo='percent+value')

    df5_piv = df5.pivot(index='distrito', columns='mes', values='total').fillna(0)
    df5_piv.columns = [
        mes_anio_anterior if col == (hoy.month - 1) else mes_anio_actual
        for col in df5_piv.columns
    ]
    fig5 = px.bar(
        df5_piv,
        barmode='group',
        title=f'Ventas por Distrito: {mes_anio_anterior} vs {mes_anio_actual}',
        labels={'value':'S/ Ingresos','distrito':'Distrito'},
        color_discrete_sequence=['#FFA600','#FF6361']
    )

    fig6 = px.pie(
        df6,
        names='tipo_cliente',
        values='cantidad',
        title=f'Usuarios Nuevos vs Recurrentes ({mes_anio_actual})'
    )
    fig6.update_traces(textinfo='percent+value')

    dash_app.layout = dash_html.Div([
        dash_html.H1('Dashboard de Análisis — MARBELLIN LENCERÍA'),
        dash_html.Div([
            dash_html.Div(dcc.Graph(figure=fig1), style={'width':'33%','padding':'10px'}),
            dash_html.Div(dcc.Graph(figure=fig2), style={'width':'33%','padding':'10px'}),
            dash_html.Div(dcc.Graph(figure=fig3), style={'width':'33%','padding':'10px'}),
        ], style={'display':'flex'}),
        dash_html.Div([
            dash_html.Div(dcc.Graph(figure=fig4), style={'width':'33%','padding':'10px'}),
            dash_html.Div(dcc.Graph(figure=fig5), style={'width':'33%','padding':'10px'}),
            dash_html.Div(dcc.Graph(figure=fig6), style={'width':'33%','padding':'10px'}),
        ], style={'display':'flex'}),
    ])

    return dash_app

dash_app = create_dashboard(server)

INDEX_HTML = """
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Ventas — Marbellin Lencería</title>
  <style>
    body { margin:20px; font-family:sans-serif; background:#f5f5f5; }
    h1 { text-align:center; }
    iframe { width:100%; height:90vh; border:none; }
  </style>
</head>
<body>
  <h1>Panel de Análisis</h1>
  <iframe src="/dashboard/"></iframe>
</body>
</html>
"""

@server.route('/')
def index():
    return render_template_string(INDEX_HTML)

if __name__ == '__main__':
    server.run(debug=True, port=8050)
