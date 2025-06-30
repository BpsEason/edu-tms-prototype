import pytest
import os
import smtplib
from unittest import mock
from main import send_email_task, check_and_notify_due_courses
from sqlalchemy import create_engine, text

# Mock database and email environment variables for testing
@pytest.fixture(autouse=True)
def mock_env_vars(mocker):
    mocker.patch.dict(os.environ, {
        'REDIS_URL': 'redis://localhost:6379/0',
        'DATABASE_URL': 'mysql+pymysql://user:pass@host:3306/db',
        'SMTP_SERVER': 'smtp.test.com',
        'SMTP_PORT': '587',
        'SMTP_USERNAME': 'user@test.com',
        'SMTP_PASSWORD': 'password',
        'MAIL_FROM_ADDRESS': 'from@test.com'
    })

def test_send_email_task_sends_email_when_configured(mocker):
    """
    Test the email sending task when SMTP credentials are provided.
    """
    mock_smtp_ssl_instance = mocker.MagicMock()
    mocker.patch('smtplib.SMTP_SSL', return_value=mock_smtp_ssl_instance)
    
    send_email_task('to@test.com', 'Test Subject', 'Test Body')

    smtplib.SMTP_SSL.assert_called_once_with('smtp.test.com', 587)
    mock_smtp_ssl_instance.__enter__.return_value.login.assert_called_once_with('user@test.com', 'password')
    mock_smtp_ssl_instance.__enter__.return_value.send_message.assert_called_once()

def test_send_email_task_skips_email_when_not_configured(mocker):
    """
    Test the email sending task when SMTP credentials are NOT provided.
    """
    mocker.patch('smtplib.SMTP_SSL', autospec=True)
    
    with mock.patch.dict(os.environ, {'SMTP_SERVER': ''}): # Set to empty to simulate missing config
        send_email_task('to@test.com', 'Test Subject', 'Test Body')

    smtplib.SMTP_SSL.assert_not_called()

def test_check_and_notify_due_courses_queries_database(mocker):
    """
    Test that the due course task queries the database and schedules emails.
    """
    # Mock the SQLAlchemy engine and session
    mock_engine = mocker.MagicMock()
    mock_session = mocker.MagicMock()
    mocker.patch('sqlalchemy.create_engine', return_value=mock_engine)
    mocker.patch('sqlalchemy.orm.sessionmaker', return_value=mocker.MagicMock(return_value=mock_session))
    
    # Mock the database query results
    mock_results = [
        ('student1@example.com', 'Python Course', '2025-07-05'),
        ('student2@example.com', 'Data Science', '2025-07-07'),
    ]
    mock_session.execute.return_value.fetchall.return_value = mock_results

    # Mock the celery task's delay method
    mock_send_email_delay = mocker.patch('main.send_email_task.delay')
    
    check_and_notify_due_courses()
    
    # Assert that the database was queried
    mock_session.execute.assert_called_once()
    mock_session.close.assert_called_once()
    
    # Assert that the `send_email_task` was scheduled for each result
    assert mock_send_email_delay.call_count == 2
    mock_send_email_delay.assert_any_call('student1@example.com', mocker.ANY, mocker.ANY)
    mock_send_email_delay.assert_any_call('student2@example.com', mocker.ANY, mocker.ANY)
