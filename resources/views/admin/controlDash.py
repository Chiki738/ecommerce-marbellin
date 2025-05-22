import os
import subprocess
import signal

def cerrar_puerto_8050():
    print("⏹️  Cerrando servidor en puerto 8050...")

    try:
        resultado = subprocess.check_output('netstat -ano | findstr :8050', shell=True)
        lineas = resultado.decode().strip().split('\n')
        pids = set()

        for linea in lineas:
            partes = linea.strip().split()
            if len(partes) >= 5:
                pid = partes[-1]
                pids.add(pid)

        for pid in pids:
            try:
                os.kill(int(pid), signal.SIGTERM)
                print(f"✅ Proceso con PID {pid} terminado.")
            except Exception as e:
                print(f"⚠️  No se pudo terminar el proceso con PID {pid}: {e}")

    except subprocess.CalledProcessError:
        print("✅ No hay ningún proceso usando el puerto 8050.")

if __name__ == "__main__":
    cerrar_puerto_8050()
 
