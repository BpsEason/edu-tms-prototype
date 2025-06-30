import os
import smtplib
from email.message import EmailMessage
from celery import Celery
from celery.schedules import crontab
from sqlalchemy import create_engine, text
from sqlalchemy.orm import sessionmaker

# Celery App Configuration
# Use Redis as the broker and backend
REDIS_URL = os.getenv('REDIS_URL', 'redis://localhost:6379/0')
celery_app = Celery('tasks', broker=REDIS_URL, backend=REDIS_URL)

# Celery Beat Schedule
celery_app.conf.beat_schedule = {
    # 'check-due-courses-every-day': {
    #     'task': 'main.check_and_notify_due_courses',
    #     'schedule': crontab(hour='8', minute='0'), # Run every day at 8 AM
    # },
    'check-due-courses-every-minute': { # For development/testing
        'task': 'main.check_and_notify_due_courses',
        'schedule': 60.0, # Run every 60 seconds
    },
}

# --- Email Sending Task ---
@celery_app.task
def send_email_task(to_email: str, subject: str, body: str):
    """
    Celery task to send an email notification.
    """
    smtp_server = os.getenv('SMTP_SERVER')
    smtp_port = os.getenv('SMTP_PORT')
    smtp_username = os.getenv('SMTP_USERNAME')
    smtp_password = os.getenv('SMTP_PASSWORD')
    mail_from_address = os.getenv('MAIL_FROM_ADDRESS')
    
    if not all([smtp_server, smtp_port, smtp_username, smtp_password, mail_from_address]):
        print("SMTP credentials are not configured. Skipping email task.")
        return
        
    msg = EmailMessage()
    msg.set_content(body)
    msg['Subject'] = subject
    msg['From'] = mail_from_address
    msg['To'] = to_email

    print(f"Attempting to send email to {to_email}...")
    try:
        with smtplib.SMTP_SSL(smtp_server, int(smtp_port)) as smtp:
            smtp.login(smtp_username, smtp_password)
            smtp.send_message(msg)
        print(f"Email successfully sent to {to_email}")
    except Exception as e:
        print(f"Failed to send email to {to_email}: {e}")
        # In a production app, you might want to log this error to Sentry/etc.
        # or retry the task after some delay.

# --- Database Check Task ---
@celery_app.task
def check_and_notify_due_courses():
    """
    Periodically checks for courses due in the next 7 days and sends notifications.
    Connects to the MySQL database to get real data.
    """
    print("Running due course check task...")
    
    # Use SQLAlchemy to connect to the Laravel MySQL database
    DATABASE_URL = os.getenv("DATABASE_URL")
    if not DATABASE_URL:
        print("DATABASE_URL is not set. Skipping database check.")
        return

    # Use pymysql dialect for SQLAlchemy
    engine = create_engine(DATABASE_URL)
    SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
    
    db_session = SessionLocal()
    try:
        # Query for assignments due in the next 7 days
        query = text("""
            SELECT u.email, c.title, a.due_date
            FROM assignments a
            JOIN users u ON a.user_id = u.id
            JOIN courses c ON a.course_id = c.id
            WHERE a.due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        """)
        results = db_session.execute(query).fetchall()
        
        for email, course_title, due_date in results:
            subject = f"課程到期提醒：{course_title}"
            body = f"您好，您的課程「{course_title}」將於 {due_date.strftime('%Y-%m-%d')} 到期，請盡快完成！"
            send_email_task.delay(email, subject, body)
            print(f"Scheduled email for {email} about course '{course_title}' due on {due_date}")

    except Exception as e:
        print(f"Database query error: {e}")
        # Raise the exception to fail the task and log it
        raise e
    finally:
        db_session.close()


# --- Minimal FastAPI app for health checks ---
# The main logic runs in Celery workers, but a small FastAPI app can be useful for health checks.
from fastapi import FastAPI
app = FastAPI()

@app.get("/")
def read_root():
    return {"status": "ok", "message": "FastAPI Celery worker is running."}
