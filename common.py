import os, time
import logging

def import_log(script_name):

    current_time = time.strftime("%Y%m%d_%H%M%S")
    parent_dir = os.path.dirname(os.path.abspath(__file__))

    log_dir = f"{parent_dir}\\log\\{script_name}\\"
    os.makedirs(log_dir, exist_ok=True)
    log_file = f"{log_dir}\\{current_time}_task.log"

    # Remove all handlers associated with the root logger object.
    for handler in logging.root.handlers[:]:
        logging.root.removeHandler(handler)

    logging.basicConfig(
        filename=log_file,
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s',
        encoding='shift_jis'
    )