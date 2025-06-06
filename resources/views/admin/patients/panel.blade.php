<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qmed Hospital - Smart Ward Patient System</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #f0f0f0;
        }
        
        .header {
            background-color: white;
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            height: 40px;
        }
        
        .logo {
            color: #00a99d;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
        }
        
        .time-info {
            display: flex;
            align-items: center;
        }
        
        .main-content {
            height: calc(100vh - 40px);
            position: relative;
        }
        
        .panel-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background: transparent;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }
        
        .patient-card {
            background-color: #00a99d;
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .patient-card h4 {
            font-size: 22px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .panel-button {
            background-color: #00a99d;
            color: white;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            height: 80px;
            font-size: 14px;
        }
        
        .panel-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .panel-button i {
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .panel-button div {
            font-size: 12px;
            line-height: 1.2;
        }
        
        .button-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .button-row .panel-button {
            flex: 1;
            width: 120px;
        }
        
        .bottom-nav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #00a99d;
            display: flex;
            justify-content: space-around;
            color: white;
            padding: 10px 0;
            height: 60px;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .nav-item i {
            font-size: 20px;
            margin-bottom: 4px;
        }
        
        .video-container {
            position: absolute;
            width: calc(100% - 280px);
            height: 100%;
            left: 0;
            top: 0;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .video-container iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .video-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            background-color: #eaeaea;
            color: #666;
            font-size: 18px;
            text-align: center;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .panel-right {
                width: 100%;
                height: auto;
                background-color: rgba(255, 255, 255, 0.8);
            }
            
            .video-container {
                width: 100%;
                z-index: -1;
            }
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notifications-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .notifications-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .notifications-header {
            background-color: #00a99d;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h5 {
            margin: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-notifications-modal {
            font-size: 24px;
            cursor: pointer;
            color: white;
        }

        .notifications-body {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .notification-item {
            background-color: #f8f9fa;
            border-left: 4px solid #00a99d;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .notification-item.unread {
            background-color: #e3f2fd;
            border-left-color: #2196f3;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .notification-item.read {
            opacity: 0.8;
        }

        .notification-from {
            font-weight: bold;
            color: #00a99d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .notification-message {
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 12px;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mark-read-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }

        .mark-read-btn:hover {
            background-color: #218838;
        }

        .no-notifications {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .no-notifications i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .alert-nurse-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .alert-nurse-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .alert-nurse-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .alert-nurse-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .alert-nurse-body {
            padding: 20px;
        }
        
        .alert-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .alert-btn {
            padding: 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            color: white;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .alert-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .emergency-btn {
            background-color: #dc3545;
        }
        
        .pain-btn {
            background-color: #fd7e14;
        }
        
        .assistance-btn {
            background-color: #6f42c1;
        }
        
        .water-btn {
            background-color: #0dcaf0;
        }
        
        .bathroom-btn {
            background-color: #198754;
        }
        
        .food-btn {
            background-color: #ffc107;
            color: #333;
        }
        
        .medical-team-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .medical-team-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .medical-team-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .medical-team-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-medical-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .medical-team-body {
            padding: 20px;
        }
        
        .team-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #00a99d;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }
        
        .team-member-card {
            display: flex;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .team-member-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            background-color: #00a99d;
            color: white;
            font-size: 30px;
        }
        
        .nurse-avatar {
            background-color: #0dcaf0;
        }
        
        .team-member-info {
            flex: 1;
            padding: 15px;
        }
        
        .member-name {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .member-specialty, .member-role, .member-qualification {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .member-contact {
            font-size: 13px;
            margin-top: 8px;
        }
        
        .member-contact div {
            margin-bottom: 3px;
        }
        
        .member-contact i {
            margin-right: 5px;
            color: #00a99d;
        }
        
        .ward-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .ward-info div {
            margin-bottom: 5px;
        }
        
        .environmental-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .environmental-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .environmental-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .environmental-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-environmental-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .environmental-body {
            padding: 20px;
        }
        
        .control-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .temperature-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .temperature-display {
            font-size: 36px;
            font-weight: bold;
            color: #00a99d;
        }
        
        .temperature-buttons {
            display: flex;
            gap: 10px;
        }
        
        .temp-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .decrease-btn {
            background-color: #e0f2f1;
            color: #00a99d;
        }
        
        .increase-btn {
            background-color: #00a99d;
            color: white;
        }
        
        .temp-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .lighting-control {
            width: 100%;
        }
        
        .light-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .light-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 80px;
            border: none;
            border-radius: 8px;
            background-color: #e0f2f1;
            color: #00a99d;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .light-btn i {
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .light-btn:hover, .light-btn.active {
            background-color: #00a99d;
            color: white;
        }
        
        .curtain-control {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        
        .curtain-btn {
            flex: 1;
            height: 60px;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            background-color: #e0f2f1;
            color: #00a99d;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .curtain-btn:hover, .curtain-btn.active {
            background-color: #00a99d;
            color: white;
        }
        
        .nurse-call-btn {
            width: 100%;
            height: 60px;
            border: none;
            border-radius: 8px;
            background-color: #dc3545;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .nurse-call-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .survey-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .survey-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .survey-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .survey-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-survey-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .survey-body {
            padding: 20px;
        }
        
        .survey-intro {
            margin-bottom: 20px;
        }
        
        .survey-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .rating-question p {
            margin-bottom: 10px;
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            padding: 0 5px;
            transition: all 0.2s;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffc107;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
        }
        
        .survey-submit {
            text-align: center;
            margin-top: 20px;
        }
        
        .submit-survey-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background-color: #00a99d;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .submit-survey-btn:hover {
            background-color: #008e82;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        .toast-notification {
            visibility: hidden;
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 25px;
            padding: 12px 25px;
            z-index: 2000;
            font-size: 14px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            min-width: 250px;
        }
        
        .toast-notification.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
        
        @media (max-width: 768px) {
            .light-options {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .admission-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .admission-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .admission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .admission-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-admission-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .admission-body {
            padding: 20px;
        }
        
        .admission-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .patient-info-section {
            background-color: #e0f2f1;
        }
        
        .patient-info-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 10px;
        }
        
        .patient-name {
            font-size: 18px;
            font-weight: 600;
            color: #00a99d;
        }
        
        .patient-mrn {
            font-size: 14px;
            color: #666;
        }
        
        .patient-demographics {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            margin-right: 8px;
        }
        
        .info-label i {
            color: #00a99d;
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }
        
        .info-value {
            color: #333;
        }
        
        .admission-notes {
            background-color: white;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #00a99d;
        }
        
        .risk-list {
            margin: 0;
            padding-left: 20px;
        }
        
        .risk-list li {
            margin-bottom: 5px;
            color: #dc3545;
        }
        
        .risk-list i {
            margin-right: 5px;
        }
        
        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .food-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .vital-signs-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .vital-signs-content {
            background-color: white;
            border-radius: 8px;
            width: 95%;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-height: 85vh;
            overflow-y: auto;
        }
        
        .vital-signs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .vital-signs-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-vital-signs-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .vital-signs-body {
            padding: 20px;
        }
        
        .vital-record {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vital-record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .vital-date {
            font-weight: 600;
            color: #00a99d;
            font-size: 16px;
        }
        
        .vital-ews {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: 600;
            min-width: 32px;
            text-align: center;
        }
        
        .ews-normal {
            background-color: #28a745;
        }
        
        .ews-low {
            background-color: #17a2b8;
        }
        
        .ews-medium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .ews-high {
            background-color: #dc3545;
        }
        
        .vital-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .vital-item {
            padding: 10px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .vital-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .vital-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .vital-notes {
            margin-top: 15px;
            padding: 10px;
            background-color: #e0f2f1;
            border-radius: 6px;
            font-style: italic;
            color: #555;
        }
        
        @media (max-width: 768px) {
            .vital-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .vital-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .food-content {
            background-color: white;
            border-radius: 8px;
            width: 95%;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-height: 85vh;
            overflow-y: auto;
        }
        
        .food-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .food-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-food-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .food-body {
            padding: 20px;
        }
        
        .food-patient-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .meal-selection {
            margin-top: 20px;
        }
        
        .meal-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }
        
        .meal-tab {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            background-color: #e9ecef;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .meal-tab.active {
            background-color: #00a99d;
            color: white;
        }
        
        .meal-category {
            margin-bottom: 15px;
            color: #343a40;
            font-weight: 600;
        }
        
        .menu-items {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 15px;
        }
        
        .menu-item {
            display: flex;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        
        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .menu-item-image {
            width: 80px;
            height: 80px;
            background-size: cover;
            background-position: center;
        }
        
        .menu-item-details {
            flex: 1;
            padding: 12px;
        }
        
        .menu-item-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .menu-item-description {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }
        
        .menu-item-nutritional {
            font-size: 12px;
            color: #00a99d;
        }
        
        .menu-item-action {
            display: flex;
            align-items: center;
            padding: 0 12px;
        }
        
        .order-btn {
            background-color: #00a99d;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .order-btn:hover {
            background-color: #008f85;
        }
        
        .order-btn.selected {
            background-color: #28a745;
        }
        
        .orders-section {
            margin-top: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
        
        .orders-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #343a40;
        }
        
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .order-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .order-meal {
            font-size: 13px;
            color: #00a99d;
            margin-bottom: 3px;
        }
        
        .order-time {
            font-size: 12px;
            color: #6c757d;
        }
        
        .order-status {
            font-size: 12px;
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            margin-top: 3px;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #343a40;
        }
        
        .status-preparing {
            background-color: #17a2b8;
        }
        
        .status-ready {
            background-color: #007bff;
        }
        
        .status-delivered {
            background-color: #28a745;
        }
        
        .status-cancelled {
            background-color: #dc3545;
        }
        
        .cancel-order {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .cancel-order:hover {
            background-color: #c82333;
        }
        
        .no-orders {
            text-align: center;
            color: #6c757d;
            padding: 15px;
        }
        
        .meal-note {
            color: #6c757d;
            font-size: 13px;
            font-style: italic;
            margin-top: 8px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Vital sign button hover effect */
        #vital-sign-btn:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        #vital-sign-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .ward-info div {
            margin-bottom: 5px;
        }
        
        .medical-info-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .medical-info-content {
            background-color: white;
            border-radius: 8px;
            width: 95%;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 85vh;
            overflow-y: auto;
        }
        
        .medical-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .medical-info-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-medical-info-modal {
            font-size: 24px;
            cursor: pointer;
        }
        
        .medical-info-body {
            padding: 20px;
        }
        
        .medical-section {
            margin-bottom: 30px;
        }
        
        .medications-list, .medical-history-list {
            margin-top: 15px;
        }
        
        .medication-item, .history-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #00a99d;
        }
        
        .medication-item.discontinued {
            opacity: 0.7;
            border-left-color: #dc3545;
        }
        
        .medication-item.paused {
            border-left-color: #ffc107;
        }
        
        .medication-header, .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .medication-name, .history-title {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }
        
        .medication-status, .history-type {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-paused {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-discontinued {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-chronic {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-resolved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .type-condition {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .type-allergy {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .type-surgery {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .type-family_history {
            background-color: #ffeaa7;
            color: #6c5500;
        }
        
        .medication-details, .history-details {
            margin-top: 10px;
        }
        
        .medication-row, .history-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .medication-row .label, .history-row .label {
            font-weight: 600;
            color: #6c757d;
            width: 120px;
            flex-shrink: 0;
        }
        
        .medication-row .value, .history-row .value {
            flex: 1;
            color: #333;
        }
        
        .severity-severe {
            color: #dc3545;
            font-weight: 600;
        }
        
        .severity-moderate {
            color: #fd7e14;
            font-weight: 600;
        }
        
        .severity-mild {
            color: #28a745;
            font-weight: 600;
        }
        
        .medical-history-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .history-tab {
            padding: 8px 15px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #6c757d;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }
        
        .history-tab.active {
            background-color: #00a99d;
            color: white;
            border-color: #00a99d;
        }
        
        .history-tab:hover {
            background-color: #f8f9fa;
        }
        
        .history-tab.active:hover {
            background-color: #008a7e;
        }
        
        .no-medications, .no-history {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .no-medications i, .no-history i {
            font-size: 48px;
            margin-bottom: 10px;
            color: #dee2e6;
        }
        
        .no-medications p, .no-history p {
            margin: 0;
            font-size: 14px;
        }
        
        @media (max-width: 576px) {
            .vital-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Enhanced Vital Signs Styles */
        .vital-sign-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .vital-sign-card.latest {
            border-left: 4px solid #00a99d;
            background: linear-gradient(135deg, #e8f5f0 0%, #f0f9f6 100%);
        }
        
        .vital-sign-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .vital-sign-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
            margin-bottom: 20px;
        }
        
        .vital-time {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 600;
            color: #00a99d;
        }
        
        .vital-time i {
            color: #6c757d;
        }
        
        .vital-recorder {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6c757d;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-radius: 15px;
        }
        
        .vital-sign-body {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .vital-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }
        
        .vital-metric {
            background: white;
            border-radius: 10px;
            padding: 15px 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #f1f3f4;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .vital-metric:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        
        .vital-metric i {
            font-size: 20px;
            color: #00a99d;
            margin-bottom: 8px;
            display: block;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #212529;
            margin: 5px 0;
            line-height: 1;
        }
        
        .metric-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .vital-ews {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
        
        .ews-score {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 25px;
            border-radius: 12px;
            color: white;
            font-weight: bold;
            min-width: 120px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .ews-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        
        .ews-value {
            font-size: 28px;
            font-weight: 900;
            line-height: 1;
        }
        
        .ews-normal {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .ews-low {
            background: linear-gradient(135deg, #17a2b8, #20c997);
        }
        
        .ews-medium {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529;
        }
        
        .ews-high {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            animation: pulse-warning 2s infinite;
        }
        
        @keyframes pulse-warning {
            0% { box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); }
            50% { box-shadow: 0 4px 16px rgba(220, 53, 69, 0.6); }
            100% { box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); }
        }
        
        .vital-notes {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #2196f3;
        }
        
        .notes-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .notes-text {
            color: #424242;
            line-height: 1.5;
            font-style: italic;
        }
        
        /* Status indicators for vital values */
        .vital-metric.critical .metric-value {
            color: #dc3545;
            animation: pulse-critical 1.5s infinite;
        }
        
        .vital-metric.warning .metric-value {
            color: #fd7e14;
        }
        
        .vital-metric.normal .metric-value {
            color: #28a745;
        }
        
        @keyframes pulse-critical {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Latest vital signs indicator */
        .vital-sign-card.latest::before {
            content: "LATEST";
            position: absolute;
            top: 10px;
            right: 10px;
            background: #00a99d;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        /* Empty state styling */
        .vital-empty {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .vital-empty i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .vital-empty p {
            font-size: 16px;
            margin: 0;
        }
        
        /* Loading state */
        .vital-loading {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .vital-loading i {
            color: #00a99d;
            margin-bottom: 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .vital-metrics {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .vital-metric {
                padding: 10px 8px;
            }
            
            .metric-value {
                font-size: 20px;
            }
            
            .vital-sign-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .vital-metrics {
                grid-template-columns: 1fr;
            }
            
            .ews-score {
                min-width: 100px;
                padding: 12px 20px;
            }
        }
        
        /* Badge styling for vital signs summary */
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }
        
        .badge-success {
            background-color: #198754;
            color: #fff;
        }
        
        .badge-info {
            background-color: #0dcaf0;
            color: #000;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        /* Health Education Modal Styles */
        .health-education-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .health-education-content {
            background-color: white;
            border-radius: 8px;
            width: 95%;
            max-width: 900px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .health-education-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .health-education-header h5 {
            margin: 0;
            font-size: 18px;
        }

        .close-health-education-modal {
            font-size: 24px;
            cursor: pointer;
        }

        .health-education-body {
            padding: 20px;
        }

        .education-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .education-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .welcome-content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 15px;
        }

        .hospital-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #00a99d;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-item i {
            color: #00a99d;
            width: 16px;
        }

        .condition-overview p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 12px;
        }

        .symptoms-section, .management-section, .diet-section, .exercise-section, .warning-section {
            margin-top: 20px;
        }

        .symptoms-section ul, .warning-list {
            list-style-type: none;
            padding: 0;
        }

        .symptoms-section li, .warning-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 25px;
        }

        .symptoms-section li:before, .warning-list li:before {
            content: "•";
            color: #00a99d;
            font-weight: bold;
            position: absolute;
            left: 8px;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .tip-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e9ecef;
            transition: transform 0.2s;
        }

        .tip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .tip-card i {
            font-size: 24px;
            color: #00a99d;
            margin-bottom: 10px;
        }

        .tip-card h6 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .tip-card p {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
            margin: 0;
        }

        .diet-recommendations {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .good-foods, .avoid-foods {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .good-foods {
            border-left: 4px solid #28a745;
        }

        .avoid-foods {
            border-left: 4px solid #ffc107;
        }

        .diet-recommendations ul {
            list-style-type: none;
            padding: 0;
            margin-top: 10px;
        }

        .diet-recommendations li {
            padding: 5px 0;
            font-size: 14px;
            position: relative;
            padding-left: 20px;
        }

        .good-foods li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .avoid-foods li:before {
            content: "⚠";
            color: #ffc107;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .exercise-list {
            margin-top: 15px;
        }

        .exercise-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .exercise-item i {
            font-size: 24px;
            color: #00a99d;
            margin-top: 5px;
        }

        .exercise-item strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .exercise-item p {
            margin: 0;
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .contact-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e9ecef;
        }

        .contact-card i {
            font-size: 32px;
            color: #00a99d;
            margin-bottom: 10px;
        }

        .contact-card h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .contact-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .text-primary { color: #0d6efd !important; }
        .text-warning { color: #ffc107 !important; }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }

        @media (max-width: 768px) {
            .tips-grid {
                grid-template-columns: 1fr;
            }
            
            .diet-recommendations {
                grid-template-columns: 1fr;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Introduction Modal Styles */
        .introduction-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .introduction-content {
            background-color: white;
            border-radius: 8px;
            width: 95%;
            max-width: 900px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .introduction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #00a99d;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .introduction-header h5 {
            margin: 0;
            font-size: 18px;
        }

        .close-introduction-modal {
            font-size: 24px;
            cursor: pointer;
        }

        .introduction-body {
            padding: 20px;
        }

        .intro-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .intro-section:last-child {
            border-bottom: none;
        }

        .hospital-logo-section {
            text-align: center;
            padding: 30px 0;
            background: linear-gradient(135deg, #00a99d, #00b8a6);
            color: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .hospital-logo {
            margin-bottom: 15px;
        }

        .hospital-name {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .hospital-tagline {
            font-size: 16px;
            font-style: italic;
            opacity: 0.9;
        }

        .welcome-message .lead {
            font-size: 18px;
            font-weight: 500;
            color: #00a99d;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #00a99d;
        }

        .feature-item i {
            color: #00a99d;
            font-size: 20px;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-card {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .info-card i {
            font-size: 28px;
            color: #00a99d;
            margin-bottom: 10px;
        }

        .info-card h6 {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .info-card p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .mission, .vision {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .mission {
            border-left: 4px solid #00a99d;
        }

        .vision {
            border-left: 4px solid #0dcaf0;
        }

        .contact-services {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .contact-info ul {
            list-style: none;
            padding: 0;
        }

        .contact-info li {
            margin-bottom: 8px;
        }

        .specialties-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .specialty-badge {
            background-color: #00a99d;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .rights-responsibilities {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .rights, .responsibilities {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .rights {
            border-left: 4px solid #28a745;
        }

        .responsibilities {
            border-left: 4px solid #ffc107;
        }

        .rights ul, .responsibilities ul {
            list-style: none;
            padding: 0;
        }

        .rights li, .responsibilities li {
            padding: 5px 0;
            font-size: 14px;
            position: relative;
            padding-left: 20px;
        }

        .rights li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .responsibilities li:before {
            content: "•";
            color: #ffc107;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .emergency-content {
            background-color: #fff5f5;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #fecaca;
        }

        .emergency-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .emergency-item:last-child {
            margin-bottom: 0;
        }

        .emergency-item i {
            font-size: 24px;
            color: #dc3545;
            margin-top: 5px;
        }

        .emergency-item strong {
            display: block;
            margin-bottom: 5px;
            color: #dc3545;
        }

        .emergency-item p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .info-cards {
                grid-template-columns: 1fr;
            }
            
            .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .contact-services {
                grid-template-columns: 1fr;
            }
            
            .rights-responsibilities {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <i class="fa fa-hospital"></i>
            Qmed Hospital - Smart Ward Patient System
        </div>
        <div class="time-info">
            <span id="current-time">15:18:36</span>
            <span id="current-date" class="mx-2">WED, 05/14/2024</span>
            <span class="mx-2"><i class="fa fa-thermometer-half"></i> 25°C</span>
            <span class="mx-2"><i class="fa fa-user"></i></span>
            <span class="mx-2"><i class="fa fa-power-off"></i></span>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Patient bed visual/video content -->
        <div class="video-container">
            <iframe 
                id="youtube-video"
                src="https://www.youtube.com/embed/LGwfL6cRpis?enablejsapi=1&autoplay=1&mute=1&loop=1&playlist=LGwfL6cRpis&controls=0&showinfo=0&rel=0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                frameborder="0">
            </iframe>
            </div>
        
        <!-- Right Side Panel -->
        <div class="panel-right">
            <!-- Patient Info Card -->
            <div class="patient-card">
                <h4>{{ $bed->bed_number }}</h4>
                <div>{{ strtoupper($patient->name) }}</div>
                <div><i class="fa fa-user-circle"></i> MRN: {{ $patient->mrn }}</div>
        </div>
        
            <!-- First row of buttons -->
            <div class="button-row">
                <div class="panel-button" id="medication-info-btn">
                    <i class="fa fa-pills"></i>
                    <div>Medication Information</div>
                </div>
                <div class="panel-button" id="food-ordering-btn">
                    <i class="fa fa-utensils"></i>
                    <div>Food Ordering</div>
                </div>
            </div>
            
            <!-- Second row of buttons -->
            <div class="button-row">
                <div class="panel-button" id="environmental-control-btn">
                    <i class="fa fa-thermometer-half"></i>
                    <div>Environmental Control</div>
                </div>
                <div class="panel-button" id="medical-team-btn">
                    <i class="fa fa-user-md"></i>
                    <div>Medical Team</div>
                    </div>
                </div>
                
            <!-- Third row of buttons -->
            <div class="button-row">
                <div class="panel-button" id="satisfaction-survey-btn">
                    <i class="fa fa-star"></i>
                    <div>Satisfaction Survey</div>
                </div>
                <div class="panel-button" id="alert-nurse-btn">
                    <i class="fa fa-bell"></i>
                    <div>Alert Nurse</div>
                </div>
            </div>
            
            <!-- New Notifications row -->
            <div class="button-row">
                <div class="panel-button" id="notifications-btn">
                    <i class="fa fa-envelope"></i>
                    <div>Notifications</div>
                    <span class="notification-badge" id="notification-badge" style="display: none;">0</span>
                </div>
                <div class="panel-button" id="vital-sign-btn" onclick="openVitalSignsModal()">
                    <i class="fa fa-heart"></i>
                    <div>Vital Sign</div>
                </div>
            </div>
            

                    </div>
                </div>
                
    <!-- Alert Nurse Modal -->
    <div class="alert-nurse-modal" id="alertModal">
        <div class="alert-nurse-content">
            <div class="alert-nurse-header">
                <h5>Alert Nursing Station</h5>
                <div class="close-modal" onclick="closeAlertModal()">&times;</div>
            </div>
            <div class="alert-nurse-body">
                <div class="alert-buttons">
                    <button class="alert-btn emergency-btn" onclick="sendAlert('emergency')">
                        <i class="fas fa-exclamation-triangle"></i><br>Emergency
                    </button>
                    <button class="alert-btn pain-btn" onclick="sendAlert('pain')">
                        <i class="fas fa-heartbeat"></i><br>Pain
                    </button>
                    <button class="alert-btn assistance-btn" onclick="sendAlert('assistance')">
                        <i class="fas fa-hands-helping"></i><br>Assistance
                    </button>
                    <button class="alert-btn water-btn" onclick="sendAlert('water')">
                        <i class="fas fa-tint"></i><br>Water
                    </button>
                    <button class="alert-btn bathroom-btn" onclick="sendAlert('bathroom')">
                        <i class="fas fa-toilet"></i><br>Bathroom
                    </button>
                    <button class="alert-btn food-btn" onclick="sendAlert('food')">
                        <i class="fas fa-utensils"></i><br>Food
                    </button>
                </div>
                <div class="alert-status mt-3" id="alertStatus" style="display: none;"></div>
            </div>
                    </div>
                </div>

    <!-- Notifications Modal -->
    <div class="notifications-modal" id="notificationsModal">
        <div class="notifications-content">
            <div class="notifications-header">
                <h5><i class="fa fa-envelope"></i> Nurse Responses</h5>
                <div class="close-notifications-modal" onclick="closeNotificationsModal()">&times;</div>
            </div>
            <div class="notifications-body" id="notificationsBody">
                <div class="no-notifications">
                    <i class="fa fa-envelope-open"></i>
                    <p>Loading notifications...</p>
                </div>
            </div>
                    </div>
                </div>
                
    <!-- Medical Team Modal -->
    <div class="medical-team-modal" id="medicalTeamModal">
        <div class="medical-team-content">
            <div class="medical-team-header">
                <h5>Medical Team</h5>
                <span class="close-medical-modal">&times;</span>
            </div>
            <div class="medical-team-body">
                <!-- Consultant Info -->
                <div class="team-section">
                    <h6 class="section-title">Consultant</h6>
                    <div class="team-member-card">
                        <div class="team-member-avatar">
                            <i class="fa fa-user-md"></i>
                        </div>
                        <div class="team-member-info">
                            <div class="member-name">{{ $activeAdmission->consultant->name ?? 'Not Assigned' }}</div>
                            <div class="member-specialty">{{ $activeAdmission->consultant->specialty->name ?? '' }}</div>
                            <div class="member-qualification">{{ $activeAdmission->consultant->qualification ?? '' }}</div>
                            <div class="member-contact">
                                @if(isset($activeAdmission->consultant))
                                <div><i class="fa fa-envelope"></i> {{ $activeAdmission->consultant->email }}</div>
                                <div><i class="fa fa-phone"></i> {{ $activeAdmission->consultant->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nurse Info -->
                <div class="team-section">
                    <h6 class="section-title">Primary Nurse</h6>
                    <div class="team-member-card">
                        <div class="team-member-avatar nurse-avatar">
                            <i class="fa fa-user-nurse"></i>
                    </div>
                        <div class="team-member-info">
                            <div class="member-name">{{ $activeAdmission->nurse->name ?? 'Not Assigned' }}</div>
                            <div class="member-role">Registered Nurse</div>
                            <div class="member-contact">
                                @if(isset($activeAdmission->nurse))
                                <div><i class="fa fa-envelope"></i> {{ $activeAdmission->nurse->email }}</div>
                                <div><i class="fa fa-phone"></i> {{ $activeAdmission->nurse->phone ?? 'Not Available' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ward Info -->
                <div class="team-section">
                    <h6 class="section-title">Ward Details</h6>
                    <div class="ward-info">
                        <div><strong>Ward:</strong> {{ $ward->name }}</div>
                        <div><strong>Bed Number:</strong> {{ $bed->bed_number }}</div>
                        <div><strong>Admitted On:</strong> {{ $activeAdmission->admission_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Health Education Modal -->
    <div class="health-education-modal" id="healthEducationModal">
        <div class="health-education-content">
            <div class="health-education-header">
                <h5><i class="fa fa-graduation-cap"></i> Health Education - Osteoarthritis</h5>
                <span class="close-health-education-modal">&times;</span>
            </div>
            <div class="health-education-body">
                <!-- Welcome Section -->
                <div class="education-section">
                    <h6 class="section-title">
                        <i class="fa fa-hospital text-primary"></i> 
                        Welcome to Qmed Hospital
                    </h6>
                    <div class="welcome-content">
                        <p>Welcome to Qmed Hospital's Smart Ward Patient System. We are committed to providing you with comprehensive care and education about your condition.</p>
                        <div class="hospital-info">
                            <div class="info-item">
                                <i class="fa fa-map-marker-alt"></i>
                                <span>123 Medical Plaza, Healthcare District</span>
                            </div>
                            <div class="info-item">
                                <i class="fa fa-phone"></i>
                                <span>+1 (555) 123-QMED</span>
                            </div>
                            <div class="info-item">
                                <i class="fa fa-envelope"></i>
                                <span>info@qmedhospital.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Osteoarthritis Education -->
                <div class="education-section">
                    <h6 class="section-title">
                        <i class="fa fa-bone text-warning"></i> 
                        Understanding Osteoarthritis
                    </h6>
                    <div class="condition-overview">
                        <p><strong>What is Osteoarthritis?</strong></p>
                        <p>Osteoarthritis (OA) is the most common form of arthritis. It occurs when the protective cartilage that cushions the ends of your bones wears down over time.</p>
                        
                        <div class="symptoms-section">
                            <h6><i class="fa fa-stethoscope"></i> Common Symptoms:</h6>
                            <ul>
                                <li>Joint pain during or after movement</li>
                                <li>Stiffness, especially in the morning</li>
                                <li>Tenderness when applying light pressure</li>
                                <li>Loss of flexibility and range of motion</li>
                                <li>Bone spurs around affected joints</li>
                            </ul>
                        </div>

                        <div class="management-section">
                            <h6><i class="fa fa-heart-pulse"></i> Management Tips:</h6>
                            <div class="tips-grid">
                                <div class="tip-card">
                                    <i class="fa fa-dumbbell"></i>
                                    <h6>Stay Active</h6>
                                    <p>Low-impact exercises like swimming, walking, and stretching can help maintain joint mobility.</p>
                                </div>
                                <div class="tip-card">
                                    <i class="fa fa-weight"></i>
                                    <h6>Maintain Healthy Weight</h6>
                                    <p>Reducing excess weight decreases stress on weight-bearing joints.</p>
                                </div>
                                <div class="tip-card">
                                    <i class="fa fa-pills"></i>
                                    <h6>Take Medications</h6>
                                    <p>Follow your prescribed medication regimen to manage pain and inflammation.</p>
                                </div>
                                <div class="tip-card">
                                    <i class="fa fa-snowflake"></i>
                                    <h6>Apply Heat/Cold</h6>
                                    <p>Use heat for stiffness and cold for acute pain and swelling.</p>
                                </div>
                            </div>
                        </div>

                        <div class="diet-section">
                            <h6><i class="fa fa-apple-alt"></i> Nutrition Guidelines:</h6>
                            <div class="diet-recommendations">
                                <div class="good-foods">
                                    <h6 class="text-success">Include in Your Diet:</h6>
                                    <ul>
                                        <li>Fish rich in omega-3 fatty acids (salmon, mackerel)</li>
                                        <li>Colorful fruits and vegetables</li>
                                        <li>Nuts and seeds</li>
                                        <li>Whole grains</li>
                                        <li>Foods rich in vitamin D and calcium</li>
                                    </ul>
                                </div>
                                <div class="avoid-foods">
                                    <h6 class="text-warning">Limit These Foods:</h6>
                                    <ul>
                                        <li>Processed and fried foods</li>
                                        <li>Sugary snacks and beverages</li>
                                        <li>Excessive red meat</li>
                                        <li>Foods high in trans fats</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="exercise-section">
                            <h6><i class="fa fa-running"></i> Recommended Exercises:</h6>
                            <div class="exercise-list">
                                <div class="exercise-item">
                                    <i class="fa fa-swimmer"></i>
                                    <div>
                                        <strong>Water Exercises</strong>
                                        <p>Swimming and water aerobics reduce joint stress while improving strength and flexibility.</p>
                                    </div>
                                </div>
                                <div class="exercise-item">
                                    <i class="fa fa-walking"></i>
                                    <div>
                                        <strong>Walking</strong>
                                        <p>Start with 10-15 minutes daily and gradually increase duration as tolerated.</p>
                                    </div>
                                </div>
                                <div class="exercise-item">
                                    <i class="fa fa-hands"></i>
                                    <div>
                                        <strong>Range of Motion</strong>
                                        <p>Gentle stretching and range of motion exercises help maintain joint flexibility.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="warning-section">
                            <h6><i class="fa fa-exclamation-triangle text-danger"></i> When to Contact Your Doctor:</h6>
                            <ul class="warning-list">
                                <li>Sudden increase in pain or swelling</li>
                                <li>Joint becomes red, warm, or tender</li>
                                <li>Inability to move the joint normally</li>
                                <li>Signs of infection (fever, unusual drainage)</li>
                                <li>Side effects from medications</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="education-section">
                    <h6 class="section-title">
                        <i class="fa fa-phone-alt text-success"></i> 
                        Need Help? Contact Us
                    </h6>
                    <div class="contact-grid">
                        <div class="contact-card">
                            <i class="fa fa-user-nurse"></i>
                            <h6>Nursing Station</h6>
                            <p>Press the call button or dial ext. 2345</p>
                        </div>
                        <div class="contact-card">
                            <i class="fa fa-user-md"></i>
                            <h6>Your Doctor</h6>
                            <p>Dr. {{ $activeAdmission->consultant->name ?? 'Smith' }}</p>
                            <p>Available during rounds: 8 AM - 12 PM</p>
                        </div>
                        <div class="contact-card">
                            <i class="fa fa-question-circle"></i>
                            <h6>Patient Coordinator</h6>
                            <p>For questions about your care plan</p>
                            <p>Dial ext. 3456</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Introduction Modal -->
    <div class="introduction-modal" id="introductionModal">
        <div class="introduction-content">
            <div class="introduction-header">
                <h5><i class="fa fa-hospital"></i> Welcome to Qmed Hospital</h5>
                <span class="close-introduction-modal">&times;</span>
            </div>
            <div class="introduction-body">
                <!-- Hospital Welcome -->
                <div class="intro-section">
                    <div class="hospital-logo-section">
                        <div class="hospital-logo">
                            <i class="fa fa-hospital fa-4x"></i>
                        </div>
                        <h2 class="hospital-name">Qmed Hospital</h2>
                        <p class="hospital-tagline">Your Health, Our Priority</p>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div class="intro-section">
                    <h6 class="section-title">
                        <i class="fa fa-heart text-danger"></i> 
                        Welcome Message
                    </h6>
                    <div class="welcome-message">
                        <p class="lead">Dear Patient,</p>
                        <p>Welcome to <strong>Qmed Hospital's Smart Ward Patient System</strong>. We are delighted to have you in our care and want to ensure your stay with us is as comfortable and healing as possible.</p>
                        
                        <p>Our state-of-the-art patient system has been designed with <strong>you</strong> in mind. Through this interactive panel, you can:</p>
                        
                        <div class="features-grid">
                            <div class="feature-item">
                                <i class="fa fa-utensils"></i>
                                <span>Order meals tailored to your dietary needs</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa fa-thermometer-half"></i>
                                <span>Control your room environment</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa fa-bell"></i>
                                <span>Alert nursing staff when you need assistance</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa fa-user-md"></i>
                                <span>View your medical team information</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa fa-pills"></i>
                                <span>Access your medication information</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa fa-graduation-cap"></i>
                                <span>Learn about your health condition</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hospital Information -->
                <div class="intro-section">
                    <h6 class="section-title">
                        <i class="fa fa-info-circle text-primary"></i> 
                        About Qmed Hospital
                    </h6>
                    <div class="hospital-info-detailed">
                        <div class="info-cards">
                            <div class="info-card">
                                <i class="fa fa-certificate"></i>
                                <h6>JCI Accredited</h6>
                                <p>Internationally recognized for quality and safety standards</p>
                            </div>
                            <div class="info-card">
                                <i class="fa fa-calendar-alt"></i>
                                <h6>Established 1985</h6>
                                <p>Over 35 years of excellence in healthcare</p>
                            </div>
                            <div class="info-card">
                                <i class="fa fa-users"></i>
                                <h6>500+ Medical Professionals</h6>
                                <p>Dedicated team of specialists and caregivers</p>
                            </div>
                            <div class="info-card">
                                <i class="fa fa-award"></i>
                                <h6>Award Winning</h6>
                                <p>Multiple healthcare excellence awards</p>
                            </div>
                        </div>
                        
                        <div class="mission-vision">
                            <div class="mission">
                                <h6><i class="fa fa-bullseye"></i> Our Mission</h6>
                                <p>To provide compassionate, high-quality healthcare services while advancing medical knowledge through innovation and research.</p>
                            </div>
                            <div class="vision">
                                <h6><i class="fa fa-eye"></i> Our Vision</h6>
                                <p>To be the leading healthcare institution, setting standards for patient care, medical excellence, and technological advancement.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Services -->
                <div class="intro-section">
                    <h6 class="section-title">
                        <i class="fa fa-phone-alt text-success"></i> 
                        Contact Information & Services
                    </h6>
                    <div class="contact-services">
                        <div class="contact-info">
                            <h6>📍 Location</h6>
                            <p>123 Medical Plaza<br>Healthcare District<br>Medical City, MC 12345</p>
                            
                            <h6>📞 Contact Numbers</h6>
                            <ul>
                                <li><strong>Main Hospital:</strong> +1 (555) 123-QMED</li>
                                <li><strong>Emergency:</strong> +1 (555) 911-HELP</li>
                                <li><strong>Patient Relations:</strong> +1 (555) 123-CARE</li>
                            </ul>
                            
                            <h6>🌐 Online</h6>
                            <ul>
                                <li><strong>Website:</strong> www.qmedhospital.com</li>
                                <li><strong>Email:</strong> info@qmedhospital.com</li>
                                <li><strong>Patient Portal:</strong> portal.qmedhospital.com</li>
                            </ul>
                        </div>
                        
                        <div class="services-info">
                            <h6>🏥 Our Specialties</h6>
                            <div class="specialties-grid">
                                <span class="specialty-badge">Cardiology</span>
                                <span class="specialty-badge">Orthopedics</span>
                                <span class="specialty-badge">Neurology</span>
                                <span class="specialty-badge">Oncology</span>
                                <span class="specialty-badge">Pediatrics</span>
                                <span class="specialty-badge">Emergency Medicine</span>
                                <span class="specialty-badge">Surgery</span>
                                <span class="specialty-badge">Internal Medicine</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Rights & Responsibilities -->
                <div class="intro-section">
                    <h6 class="section-title">
                        <i class="fa fa-shield-alt text-warning"></i> 
                        Your Rights & Responsibilities
                    </h6>
                    <div class="rights-responsibilities">
                        <div class="rights">
                            <h6>Your Rights as a Patient:</h6>
                            <ul>
                                <li>Receive respectful and considerate care</li>
                                <li>Privacy and confidentiality of your medical information</li>
                                <li>Participate in decisions about your care</li>
                                <li>Access your medical records</li>
                                <li>Voice complaints without fear of retaliation</li>
                            </ul>
                        </div>
                        <div class="responsibilities">
                            <h6>Your Responsibilities:</h6>
                            <ul>
                                <li>Provide accurate and complete health information</li>
                                <li>Follow prescribed treatment plans</li>
                                <li>Ask questions when you don't understand</li>
                                <li>Treat staff and other patients with respect</li>
                                <li>Follow hospital policies and procedures</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Emergency Information -->
                <div class="intro-section emergency-info">
                    <h6 class="section-title">
                        <i class="fa fa-exclamation-triangle text-danger"></i> 
                        Emergency Information
                    </h6>
                    <div class="emergency-content">
                        <div class="emergency-item">
                            <i class="fa fa-bell"></i>
                            <div>
                                <strong>Call Button</strong>
                                <p>Press the red call button next to your bed for immediate assistance</p>
                            </div>
                        </div>
                        <div class="emergency-item">
                            <i class="fa fa-fire-extinguisher"></i>
                            <div>
                                <strong>Fire Emergency</strong>
                                <p>Pull the fire alarm and follow evacuation procedures posted in your room</p>
                            </div>
                        </div>
                        <div class="emergency-item">
                            <i class="fa fa-heartbeat"></i>
                            <div>
                                <strong>Medical Emergency</strong>
                                <p>Press the emergency button or call extension 911 from your bedside phone</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Environmental Control Modal -->
    <div class="environmental-modal" id="environmentalModal">
        <div class="environmental-content">
            <div class="environmental-header">
                <h5>Environmental Control</h5>
                <span class="close-environmental-modal">&times;</span>
            </div>
            <div class="environmental-body">
                <!-- Temperature Control -->
                <div class="control-section">
                    <h6 class="section-title">Temperature Control</h6>
                    <div class="temperature-control">
                        <div class="temperature-display">
                            <span id="current-temp">24</span>°C
                        </div>
                        <div class="temperature-buttons">
                            <button class="temp-btn decrease-btn" id="decreaseTemp">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button class="temp-btn increase-btn" id="increaseTemp">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Lighting Control -->
                <div class="control-section">
                    <h6 class="section-title">Lighting Control</h6>
                    <div class="lighting-control">
                        <div class="light-options">
                            <button class="light-btn" id="lightOff">
                                <i class="fa fa-lightbulb"></i>
                                <span>Off</span>
                            </button>
                            <button class="light-btn" id="lightDim">
                                <i class="fa fa-lightbulb"></i>
                                <span>Dim</span>
                            </button>
                            <button class="light-btn" id="lightOn">
                                <i class="fa fa-lightbulb"></i>
                                <span>On</span>
                            </button>
                            <button class="light-btn" id="lightReading">
                                <i class="fa fa-book-reader"></i>
                                <span>Reading</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Curtains Control -->
                <div class="control-section">
                    <h6 class="section-title">Curtains</h6>
                    <div class="curtain-control">
                        <button class="curtain-btn" id="curtainOpen">
                            <i class="fa fa-chevron-right"></i>
                            <span>Open</span>
                        </button>
                        <button class="curtain-btn" id="curtainClose">
                            <i class="fa fa-chevron-left"></i>
                            <span>Close</span>
                        </button>
                    </div>
                </div>
                
                <!-- Nurse Call -->
                <div class="control-section">
                    <h6 class="section-title">Need Help?</h6>
                    <button class="nurse-call-btn" id="nurseCallBtn">
                        <i class="fa fa-bell"></i>
                        <span>Call Nurse for Assistance</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Satisfaction Survey Modal -->
    <div class="survey-modal" id="surveyModal">
        <div class="survey-content">
            <div class="survey-header">
                <h5>Patient Satisfaction Survey</h5>
                <span class="close-survey-modal">&times;</span>
            </div>
            <div class="survey-body">
                <form id="satisfactionForm">
                    <!-- Introduction -->
                    <div class="survey-intro">
                        <p>Your feedback is important to us. Please help us improve our services by completing this short survey.</p>
                    </div>
                    
                    <!-- Care Quality Rating -->
                    <div class="survey-section">
                        <h6 class="section-title">Quality of Care</h6>
                        <div class="rating-question">
                            <p>How would you rate the overall quality of care received?</p>
                            <div class="star-rating">
                                <input type="radio" id="care5" name="care_rating" value="5" class="sr-only">
                                <label for="care5"><i class="fa fa-star"></i></label>
                                <input type="radio" id="care4" name="care_rating" value="4" class="sr-only">
                                <label for="care4"><i class="fa fa-star"></i></label>
                                <input type="radio" id="care3" name="care_rating" value="3" class="sr-only">
                                <label for="care3"><i class="fa fa-star"></i></label>
                                <input type="radio" id="care2" name="care_rating" value="2" class="sr-only">
                                <label for="care2"><i class="fa fa-star"></i></label>
                                <input type="radio" id="care1" name="care_rating" value="1" class="sr-only">
                                <label for="care1"><i class="fa fa-star"></i></label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Staff Responsiveness -->
                    <div class="survey-section">
                        <h6 class="section-title">Staff Responsiveness</h6>
                        <div class="rating-question">
                            <p>How responsive were the staff to your needs and concerns?</p>
                            <div class="star-rating">
                                <input type="radio" id="staff5" name="staff_rating" value="5" class="sr-only">
                                <label for="staff5"><i class="fa fa-star"></i></label>
                                <input type="radio" id="staff4" name="staff_rating" value="4" class="sr-only">
                                <label for="staff4"><i class="fa fa-star"></i></label>
                                <input type="radio" id="staff3" name="staff_rating" value="3" class="sr-only">
                                <label for="staff3"><i class="fa fa-star"></i></label>
                                <input type="radio" id="staff2" name="staff_rating" value="2" class="sr-only">
                                <label for="staff2"><i class="fa fa-star"></i></label>
                                <input type="radio" id="staff1" name="staff_rating" value="1" class="sr-only">
                                <label for="staff1"><i class="fa fa-star"></i></label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cleanliness -->
                    <div class="survey-section">
                        <h6 class="section-title">Cleanliness</h6>
                        <div class="rating-question">
                            <p>How would you rate the cleanliness of your room and facility?</p>
                            <div class="star-rating">
                                <input type="radio" id="clean5" name="clean_rating" value="5" class="sr-only">
                                <label for="clean5"><i class="fa fa-star"></i></label>
                                <input type="radio" id="clean4" name="clean_rating" value="4" class="sr-only">
                                <label for="clean4"><i class="fa fa-star"></i></label>
                                <input type="radio" id="clean3" name="clean_rating" value="3" class="sr-only">
                                <label for="clean3"><i class="fa fa-star"></i></label>
                                <input type="radio" id="clean2" name="clean_rating" value="2" class="sr-only">
                                <label for="clean2"><i class="fa fa-star"></i></label>
                                <input type="radio" id="clean1" name="clean_rating" value="1" class="sr-only">
                                <label for="clean1"><i class="fa fa-star"></i></label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Communication -->
                    <div class="survey-section">
                        <h6 class="section-title">Doctor Communication</h6>
                        <div class="rating-question">
                            <p>How clearly did doctors communicate about your condition and treatment?</p>
                            <div class="star-rating">
                                <input type="radio" id="comm5" name="comm_rating" value="5" class="sr-only">
                                <label for="comm5"><i class="fa fa-star"></i></label>
                                <input type="radio" id="comm4" name="comm_rating" value="4" class="sr-only">
                                <label for="comm4"><i class="fa fa-star"></i></label>
                                <input type="radio" id="comm3" name="comm_rating" value="3" class="sr-only">
                                <label for="comm3"><i class="fa fa-star"></i></label>
                                <input type="radio" id="comm2" name="comm_rating" value="2" class="sr-only">
                                <label for="comm2"><i class="fa fa-star"></i></label>
                                <input type="radio" id="comm1" name="comm_rating" value="1" class="sr-only">
                                <label for="comm1"><i class="fa fa-star"></i></label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Comments -->
                    <div class="survey-section">
                        <h6 class="section-title">Additional Comments</h6>
                        <div class="form-group">
                            <textarea class="form-control" id="comments" rows="4" placeholder="Please share any additional feedback, suggestions, or concerns..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="survey-submit">
                        <button type="submit" class="submit-survey-btn">
                            <i class="fa fa-paper-plane"></i>
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Admission Information Modal -->
    <div class="admission-modal" id="admissionModal">
        <div class="admission-content">
            <div class="admission-header">
                <h5>Admission Information</h5>
                <span class="close-admission-modal">&times;</span>
            </div>
            <div class="admission-body">
                <!-- Patient Information -->
                <div class="admission-section patient-info-section">
                    <div class="patient-info-header">
                        <div class="patient-name">{{ $patient->name }}</div>
                        <div class="patient-mrn">MRN: {{ $patient->mrn }}</div>
                    </div>
                    <div class="patient-demographics">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-user"></i> Age:</span>
                            <span class="info-value">{{ $patient->age }} years</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-venus-mars"></i> Gender:</span>
                            <span class="info-value">{{ ucfirst($patient->gender) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-id-card"></i> ID:</span>
                            <span class="info-value">{{ $patient->identity_number }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Admission Details -->
                <div class="admission-section">
                    <h6 class="section-title">Admission Details</h6>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-calendar-check"></i> Admitted On:</span>
                            <span class="info-value">{{ $activeAdmission->admission_date->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-clock"></i> Duration:</span>
                            <span class="info-value" id="admissionDuration">
                                {{ $activeAdmission->admission_date->diffForHumans(null, true) }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-hospital"></i> Ward:</span>
                            <span class="info-value">{{ $ward->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-bed"></i> Bed Number:</span>
                            <span class="info-value">{{ $bed->bed_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-user-md"></i> Consultant:</span>
                            <span class="info-value">{{ $activeAdmission->consultant->name ?? 'Not Assigned' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fa fa-stethoscope"></i> Specialty:</span>
                            <span class="info-value">{{ $activeAdmission->consultant->specialty->name ?? 'General' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Admission Notes -->
                <div class="admission-section">
                    <h6 class="section-title">Admission Notes</h6>
                    <div class="admission-notes">
                        <p>{{ $activeAdmission->admission_notes ?? 'No specific notes provided for this admission.' }}</p>
                    </div>
                </div>
                
                <!-- Risk Factors -->
                @if(isset($activeAdmission->risk_factors) && is_array($activeAdmission->risk_factors) && count($activeAdmission->risk_factors) > 0)
                <div class="admission-section">
                    <h6 class="section-title">Risk Factors</h6>
                    <div class="risk-factors">
                        <ul class="risk-list">
                            @foreach($activeAdmission->risk_factors as $risk)
                            <li><i class="fa fa-exclamation-triangle"></i> {{ $risk }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                <!-- Admitted By -->
                <div class="admission-section">
                    <h6 class="section-title">Admission Staff</h6>
                    <div class="info-item">
                        <span class="info-label"><i class="fa fa-user-nurse"></i> Admitted By:</span>
                        <span class="info-value">{{ $activeAdmission->admittedBy->name ?? 'Not Available' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Food Ordering Modal -->
    <div class="food-modal" id="foodOrderingModal">
        <div class="food-content">
            <div class="food-header">
                <h5>Meal Ordering System</h5>
                <span class="close-food-modal">&times;</span>
            </div>
            <div class="food-body">
                <!-- Patient Info for Meal -->
                <div class="food-patient-info">
                    <div class="patient-meal-details">
                        <div><strong>Patient:</strong> {{ $patient->name }}</div>
                        <div><strong>MRN:</strong> {{ $patient->mrn }}</div>
                        <div><strong>Ward:</strong> {{ $ward->name }}</div>
                        <div><strong>Bed:</strong> {{ $bed->bed_number }}</div>
                    </div>
                    
                    <div class="dietary-restrictions">
                        <label for="dietaryRestrictions">Dietary Restrictions:</label>
                        <select id="dietaryRestrictions" class="form-select">
                            <option value="">None</option>
                            <option value="Vegetarian">Vegetarian</option>
                            <option value="Vegan">Vegan</option>
                            <option value="Gluten-Free">Gluten Free</option>
                            <option value="Diabetic">Diabetic</option>
                            <option value="Low-Sodium">Low Sodium</option>
                            <option value="Halal">Halal</option>
                            <option value="Kosher">Kosher</option>
                        </select>
                    </div>
                </div>
                
                <div class="alert alert-info" style="border-left: 4px solid #00a99d; padding: 10px 15px; margin-bottom: 15px; background-color: #e0f2f1; border-radius: 4px;">
                    <i class="fas fa-info-circle mr-2"></i> <strong>How to order:</strong> Select one meal from each category (Breakfast, Lunch, Dinner, and Snacks). Your most recent selection will replace any previous order for that meal type. Orders for today must be placed before 7:00 AM.
                </div>
                
                <!-- Date selection for ordering -->
                <div class="date-selection" style="margin-bottom: 15px;">
                    <label for="orderDate"><strong>Order for date:</strong></label>
                    <select id="orderDate" class="form-select" style="width: auto; display: inline-block; margin-left: 10px;">
                        @php
                            $today = \Carbon\Carbon::now();
                            $tomorrow = \Carbon\Carbon::tomorrow();
                            $dayAfterTomorrow = \Carbon\Carbon::tomorrow()->addDay();
                        @endphp
                        <option value="{{ $today->format('Y-m-d') }}">Today ({{ $today->format('D, M j') }})</option>
                        <option value="{{ $tomorrow->format('Y-m-d') }}" selected>Tomorrow ({{ $tomorrow->format('D, M j') }})</option>
                        <option value="{{ $dayAfterTomorrow->format('Y-m-d') }}">{{ $dayAfterTomorrow->format('D, M j') }}</option>
                    </select>
                </div>
                
                <!-- Meal Selection Tabs -->
                <div class="meal-selection">
                    <div class="meal-tabs">
                        <button class="meal-tab active" data-meal="Breakfast">Breakfast</button>
                        <button class="meal-tab" data-meal="Lunch">Lunch</button>
                        <button class="meal-tab" data-meal="Dinner">Dinner</button>
                        <button class="meal-tab" data-meal="Snack">Snacks</button>
                    </div>
                </div>
                
                <!-- Meal Options -->
                <div class="meal-options" id="Breakfast-options">
                    <h6 class="meal-category">Breakfast Options</h6>
                    <div class="menu-items">
                        @forelse($menuItems['Breakfast'] as $item)
                            <div class="menu-item">
                                <div class="menu-item-image" style="background-image: url('https://via.placeholder.com/100?text={{ urlencode($item->name) }}')"></div>
                                <div class="menu-item-details">
                                    <div class="menu-item-title">{{ $item->name }}</div>
                                    <div class="menu-item-description">{{ $item->description ?? 'No description available' }}</div>
                                    <div class="menu-item-nutritional">{{ $item->dietary_tags ?? 'No nutritional info available' }}</div>
                                </div>
                                <div class="menu-item-action">
                                    <button class="order-btn" data-item="{{ $item->name }}" data-meal="Breakfast">Order</button>
                                </div>
                            </div>
                        @empty
                            <div>No breakfast options available.</div>
                        @endforelse
                    </div>
                </div>
                
                    <!-- Breakfast Options -->
                    <div class="meal-options" id="Breakfast-options">
                        <h6 class="meal-category">Breakfast Options</h6>
                        <div class="menu-items">
                            @forelse($menuItems['Breakfast'] as $item)
                                <div class="menu-item">
                                    <div class="menu-item-image" style="background-image: url('https://via.placeholder.com/100?text={{ urlencode($item->name) }}')"></div>
                                    <div class="menu-item-details">
                                        <div class="menu-item-title">{{ $item->name }}</div>
                                        <div class="menu-item-description">{{ $item->description ?? 'No description available' }}</div>
                                        <div class="menu-item-nutritional">{{ $item->dietary_tags ?? 'No nutritional info available' }}</div>
                                    </div>
                                    <div class="menu-item-action">
                                        <button class="order-btn" data-item="{{ $item->name }}" data-meal="Breakfast">Order</button>
                                    </div>
                                </div>
                            @empty
                                <div>No breakfast options available.</div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Lunch Options -->
                    <div class="meal-options" id="Lunch-options" style="display: none;">
                        <h6 class="meal-category">Lunch Options</h6>
                        <div class="menu-items">
                            @forelse($menuItems['Lunch'] as $item)
                                <div class="menu-item">
                                    <div class="menu-item-image" style="background-image: url('https://via.placeholder.com/100?text={{ urlencode($item->name) }}')"></div>
                                    <div class="menu-item-details">
                                        <div class="menu-item-title">{{ $item->name }}</div>
                                        <div class="menu-item-description">{{ $item->description ?? 'No description available' }}</div>
                                        <div class="menu-item-nutritional">{{ $item->dietary_tags ?? 'No nutritional info available' }}</div>
                                    </div>
                                    <div class="menu-item-action">
                                        <button class="order-btn" data-item="{{ $item->name }}" data-meal="Lunch">Order</button>
                                    </div>
                                </div>
                            @empty
                                <div>No lunch options available.</div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Dinner Options -->
                    <div class="meal-options" id="Dinner-options" style="display: none;">
                        <h6 class="meal-category">Dinner Options</h6>
                        <div class="menu-items">
                            @forelse($menuItems['Dinner'] as $item)
                                <div class="menu-item">
                                    <div class="menu-item-image" style="background-image: url('https://via.placeholder.com/100?text={{ urlencode($item->name) }}')"></div>
                                    <div class="menu-item-details">
                                        <div class="menu-item-title">{{ $item->name }}</div>
                                        <div class="menu-item-description">{{ $item->description ?? 'No description available' }}</div>
                                        <div class="menu-item-nutritional">{{ $item->dietary_tags ?? 'No nutritional info available' }}</div>
                                    </div>
                                    <div class="menu-item-action">
                                        <button class="order-btn" data-item="{{ $item->name }}" data-meal="Dinner">Order</button>
                                    </div>
                                </div>
                            @empty
                                <div>No dinner options available.</div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Snack Options -->
                    <div class="meal-options" id="Snack-options" style="display: none;">
                        <h6 class="meal-category">Snack Options</h6>
                        <div class="menu-items">
                            @forelse($menuItems['Snack'] as $item)
                                <div class="menu-item">
                                    <div class="menu-item-image" style="background-image: url('https://via.placeholder.com/100?text={{ urlencode($item->name) }}')"></div>
                                    <div class="menu-item-details">
                                        <div class="menu-item-title">{{ $item->name }}</div>
                                        <div class="menu-item-description">{{ $item->description ?? 'No description available' }}</div>
                                        <div class="menu-item-nutritional">{{ $item->dietary_tags ?? 'No nutritional info available' }}</div>
                                    </div>
                                    <div class="menu-item-action">
                                        <button class="order-btn" data-item="{{ $item->name }}" data-meal="Snack">Order</button>
                                    </div>
                                </div>
                            @empty
                                <div>No snack options available.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Current Orders Section -->
                <div class="orders-section">
                    <h6 class="orders-title">Your Current Orders</h6>
                    
                    <!-- Summary of current selections -->
                    <div class="current-selections" style="margin-bottom: 15px; background-color: #f8f9fa; border-radius: 6px; padding: 12px; border-left: 4px solid #00a99d;">
                        <h6 style="margin-bottom: 10px; color: #00a99d;">Your meal selections for <span id="selected-date"></span>:</h6>
                        <div class="meal-summary-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <div class="meal-summary" id="breakfast-summary">
                                <strong>Breakfast:</strong> <span class="meal-choice">None selected</span>
                            </div>
                            <div class="meal-summary" id="lunch-summary">
                                <strong>Lunch:</strong> <span class="meal-choice">None selected</span>
                            </div>
                            <div class="meal-summary" id="dinner-summary">
                                <strong>Dinner:</strong> <span class="meal-choice">None selected</span>
                            </div>
                            <div class="meal-summary" id="snack-summary">
                                <strong>Snack:</strong> <span class="meal-choice">None selected</span>
                            </div>
                        </div>
                        
                        <!-- Submit order button -->
                        <div style="text-align: center; margin-top: 15px;">
                            <button id="submit-order-btn" class="btn btn-primary" style="background-color: #00a99d; border-color: #00a99d; padding: 8px 20px; font-weight: bold;">
                                <i class="fas fa-check-circle"></i> Submit Order
                            </button>
                            <p style="margin-top: 8px; font-size: 12px; color: #6c757d;">
                                <i class="fas fa-info-circle"></i> Click submit to finalize your meal selections
                            </p>
                        </div>
                    </div>
                    
                    <div class="orders-list" id="orders-list">
                        <h6 style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px;">Pending & Upcoming Orders</h6>
                        @forelse($activeOrders as $order)
                            <div class="order-item" id="order-{{ $order->id }}">
                                <div class="order-info">
                                    <div class="order-name">{{ $order->item_name }}</div>
                                    <div class="order-meal">{{ $order->meal_type }} {{ $order->dietary_restriction ? "({$order->dietary_restriction})" : '' }}</div>
                                    <div class="order-time">
                                        <div><strong>Ordered:</strong> {{ $order->order_time->format('d M Y, h:i A') }}</div>
                                    </div>
                                    <div class="order-status status-{{ $order->status }}">{{ ucfirst($order->status) }}</div>
                                </div>
                                @if(in_array($order->status, ['pending', 'preparing']))
                                    <button class="cancel-order" data-order-id="{{ $order->id }}">Cancel</button>
                                @endif
                            </div>
                        @empty
                            <div class="no-orders">No meals ordered yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-item" id="introduction-btn">
            <i class="fa fa-info-circle"></i>
            <span>Introduction</span>
        </div>
        <div class="nav-item" id="admission-info-btn">
            <i class="fa fa-hospital-user"></i>
            <span>Admission Information</span>
        </div>
        <div class="nav-item" id="health-education-btn">
            <i class="fa fa-graduation-cap"></i>
            <span>Health Education</span>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current time and update every second
            function updateClock() {
            const now = new Date();
                let hours = now.getHours();
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                document.getElementById('current-time').textContent = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
            }
            setInterval(updateClock, 1000);
            updateClock();
        
        // Alert Nurse functionality
        const alertNurseBtn = document.getElementById('alert-nurse-btn');
            const alertModal = document.getElementById('alertModal');
        
            if (alertNurseBtn) {
        alertNurseBtn.addEventListener('click', function() {
                    alertModal.style.display = 'flex';
                });
            }

        // Notifications functionality
        const notificationsBtn = document.getElementById('notifications-btn');
        const notificationsModal = document.getElementById('notificationsModal');
        
        if (notificationsBtn) {
            notificationsBtn.addEventListener('click', function() {
                notificationsModal.style.display = 'flex';
                loadNotifications();
                });
            }

            // Food Ordering button
            const foodOrderingBtn = document.getElementById('food-ordering-btn');
            const foodOrderingModal = document.getElementById('foodOrderingModal');
            if (foodOrderingBtn && foodOrderingModal) {
                foodOrderingBtn.addEventListener('click', function() {
                    foodOrderingModal.style.display = 'flex';
                });
            }

            // Environmental Control button
            const environmentalControlBtn = document.getElementById('environmental-control-btn');
            const environmentalModal = document.getElementById('environmentalModal');
            if (environmentalControlBtn && environmentalModal) {
                environmentalControlBtn.addEventListener('click', function() {
                    environmentalModal.style.display = 'flex';
                });
            }

            // Medical Team button
            const medicalTeamBtn = document.getElementById('medical-team-btn');
            const medicalTeamModal = document.getElementById('medicalTeamModal');
            if (medicalTeamBtn && medicalTeamModal) {
                medicalTeamBtn.addEventListener('click', function() {
                    medicalTeamModal.style.display = 'flex';
                });
            }

            // Satisfaction Survey button
            const satisfactionSurveyBtn = document.getElementById('satisfaction-survey-btn');
            const surveyModal = document.getElementById('surveyModal');
            if (satisfactionSurveyBtn && surveyModal) {
                satisfactionSurveyBtn.addEventListener('click', function() {
                    surveyModal.style.display = 'flex';
                });
            }

            // Admission Info button
            const admissionInfoBtn = document.getElementById('admission-info-btn');
            const admissionModal = document.getElementById('admissionModal');
            if (admissionInfoBtn && admissionModal) {
                admissionInfoBtn.addEventListener('click', function() {
                    admissionModal.style.display = 'flex';
                });
            }

            // Add close button event listeners
            const closeMedicalModalBtn = document.querySelector('.close-medical-modal');
            if (closeMedicalModalBtn) {
                closeMedicalModalBtn.addEventListener('click', closeMedicalTeamModal);
            }

            const closeEnvironmentalModalBtn = document.querySelector('.close-environmental-modal');
            if (closeEnvironmentalModalBtn) {
                closeEnvironmentalModalBtn.addEventListener('click', closeEnvironmentalModal);
            }

            const closeSurveyModalBtn = document.querySelector('.close-survey-modal');
            if (closeSurveyModalBtn) {
                closeSurveyModalBtn.addEventListener('click', closeSurveyModal);
            }

            const closeAdmissionModalBtn = document.querySelector('.close-admission-modal');
            if (closeAdmissionModalBtn) {
                closeAdmissionModalBtn.addEventListener('click', closeAdmissionModal);
            }

            const closeFoodModalBtn = document.querySelector('.close-food-modal');
            if (closeFoodModalBtn) {
                closeFoodModalBtn.addEventListener('click', closeFoodOrderingModal);
            }

            // Medical Information button
            const medicationInfoBtn = document.getElementById('medication-info-btn');
            const medicalInfoModal = document.getElementById('medicalInfoModal');
            if (medicationInfoBtn && medicalInfoModal) {
                medicationInfoBtn.addEventListener('click', function() {
                    medicalInfoModal.style.display = 'flex';
                });
            }

            // Close Medical Information modal
            const closeMedicalInfoModalBtn = document.querySelector('.close-medical-info-modal');
            if (closeMedicalInfoModalBtn) {
                closeMedicalInfoModalBtn.addEventListener('click', closeMedicalInfoModal);
            }

            // Introduction button (bottom nav)
            const introductionBtn = document.getElementById('introduction-btn');
            const introductionModal = document.getElementById('introductionModal');
            if (introductionBtn && introductionModal) {
                introductionBtn.addEventListener('click', function() {
                    introductionModal.style.display = 'flex';
                });
            }

            // Close Introduction modal
            const closeIntroductionModalBtn = document.querySelector('.close-introduction-modal');
            if (closeIntroductionModalBtn) {
                closeIntroductionModalBtn.addEventListener('click', closeIntroductionModal);
            }

            // Health Education button (bottom nav)
            const healthEducationBtn = document.getElementById('health-education-btn');
            const healthEducationModal = document.getElementById('healthEducationModal');
            if (healthEducationBtn && healthEducationModal) {
                healthEducationBtn.addEventListener('click', function() {
                    healthEducationModal.style.display = 'flex';
                });
            }

            // Close Health Education modal
            const closeHealthEducationModalBtn = document.querySelector('.close-health-education-modal');
            if (closeHealthEducationModalBtn) {
                closeHealthEducationModalBtn.addEventListener('click', closeHealthEducationModal);
            }

            // Vital Sign button - now handled by onclick in HTML

            // Close Vital Signs modal
            const closeVitalSignsModalBtn = document.querySelector('.close-vital-signs-modal');
            if (closeVitalSignsModalBtn) {
                closeVitalSignsModalBtn.addEventListener('click', closeVitalSignsModal);
            }

            // Add click-outside-to-close functionality for all modals
            const modals = [
                { modal: alertModal, closeFunction: closeAlertModal },
                { modal: notificationsModal, closeFunction: closeNotificationsModal },
                { modal: medicalTeamModal, closeFunction: closeMedicalTeamModal },
                { modal: environmentalModal, closeFunction: closeEnvironmentalModal },
                { modal: surveyModal, closeFunction: closeSurveyModal },
                { modal: admissionModal, closeFunction: closeAdmissionModal },
                { modal: foodOrderingModal, closeFunction: closeFoodOrderingModal },
                { modal: vitalSignsModal, closeFunction: closeVitalSignsModal },
                { modal: healthEducationModal, closeFunction: closeHealthEducationModal },
                { modal: introductionModal, closeFunction: closeIntroductionModal }
            ];

            modals.forEach(({ modal, closeFunction }) => {
                if (modal) {
                    // Click outside to close modal
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeFunction();
                        }
                    });

                    // Prevent modal from closing when clicking on content
                    const modalContent = modal.querySelector('[class*="-content"]');
                    if (modalContent) {
                        modalContent.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                }
            });

            // Add Escape key functionality for all modals
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    modals.forEach(({ modal, closeFunction }) => {
                        if (modal && modal.style.display === 'flex') {
                            closeFunction();
                        }
                    });
                }
            });
        });
        
        // Close alert modal
        function closeAlertModal() {
            document.getElementById('alertModal').style.display = 'none';
            document.getElementById('alertStatus').style.display = 'none';
        }

        // Close notifications modal
        function closeNotificationsModal() {
            document.getElementById('notificationsModal').style.display = 'none';
        }

        // Load notifications from server
        function loadNotifications() {
            const notificationsBody = document.getElementById('notificationsBody');
            notificationsBody.innerHTML = '<div class="no-notifications"><i class="fa fa-spinner fa-spin"></i><p>Loading notifications...</p></div>';

            fetch(`/admin/patients/{{ $patient->id }}/notifications`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                displayNotifications(data.notifications || []);
                updateNotificationBadge(data.unread_count || 0);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsBody.innerHTML = '<div class="no-notifications"><i class="fa fa-exclamation-triangle text-warning"></i><p>Error loading notifications. Please try again.</p></div>';
            });
        }

        // Display notifications in the modal
        function displayNotifications(notifications) {
            const notificationsBody = document.getElementById('notificationsBody');
            
            if (notifications.length === 0) {
                notificationsBody.innerHTML = '<div class="no-notifications"><i class="fa fa-envelope-open"></i><p>No nurse responses yet.</p></div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const isUnread = !notification.read_at;
                const timeAgo = formatTimeAgo(new Date(notification.created_at));
                
                html += `
                    <div class="notification-item ${isUnread ? 'unread' : 'read'}" data-id="${notification.id}">
                        <div class="notification-from">
                            <i class="fa fa-user-nurse"></i>
                            Response from ${notification.nurse_name}
                        </div>
                        <div class="notification-message">
                            ${notification.response_message}
                        </div>
                        <div class="notification-time">
                            <span><i class="fa fa-clock"></i> ${timeAgo}</span>
                            ${isUnread ? '<button class="mark-read-btn" onclick="markAsRead(' + notification.id + ')">Mark as Read</button>' : '<span class="text-success"><i class="fa fa-check"></i> Read</span>'}
                        </div>
                    </div>
                `;
            });

            notificationsBody.innerHTML = html;
        }

        // Mark notification as read
        function markAsRead(notificationId) {
            fetch(`/admin/patients/{{ $patient->id }}/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification item
                    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                        notificationItem.classList.add('read');
                        
                        // Update the mark as read button
                        const markReadBtn = notificationItem.querySelector('.mark-read-btn');
                        if (markReadBtn) {
                            markReadBtn.outerHTML = '<span class="text-success"><i class="fa fa-check"></i> Read</span>';
                        }
                    }
                    
                    // Refresh notifications to update badge
                    loadNotificationCount();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Load notification count for badge
        function loadNotificationCount() {
            fetch(`/admin/patients/{{ $patient->id }}/notifications/count`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count || 0);
            })
            .catch(error => {
                console.error('Error loading notification count:', error);
            });
        }

        // Format time ago helper function
        function formatTimeAgo(date) {
            const now = new Date();
            const diff = now - date;
            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 0) {
                return days === 1 ? '1 day ago' : `${days} days ago`;
            } else if (hours > 0) {
                return hours === 1 ? '1 hour ago' : `${hours} hours ago`;
            } else if (minutes > 0) {
                return minutes === 1 ? '1 minute ago' : `${minutes} minutes ago`;
            } else {
                return 'Just now';
            }
        }

        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotificationCount();
            
            // Refresh notification count every 30 seconds
            setInterval(loadNotificationCount, 30000);
        });

        // Vital Signs Graph Variables
        let vitalSignsChart = null;

        // Load Vital Signs Graph Data
        function loadVitalSignsGraph() {
            // Show loading state
            document.getElementById('vital-loading-graph').style.display = 'block';
            document.getElementById('vital-graph-content').style.display = 'none';
            
            fetch(`/admin/vital-signs/trend/{{ $patient->id }}/data`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading
                document.getElementById('vital-loading-graph').style.display = 'none';
                
                if (data.success && data.vitals && data.vitals.length > 0) {
                    // Show graph content
                    document.getElementById('vital-graph-content').style.display = 'block';
                    createVitalSignsChart(data.vitals);
                } else {
                    showGraphError('No vital signs data available');
                }
            })
            .catch(error => {
                console.error('Error loading vital signs graph:', error);
                document.getElementById('vital-loading-graph').style.display = 'none';
                showGraphError('Error loading graph data');
            });
        }

        // Create Vital Signs Chart
        function createVitalSignsChart(vitals) {
            const ctx = document.getElementById('vitalSignsChart').getContext('2d');
            
            // Prepare data
            const labels = vitals.map(v => new Date(v.recorded_at).toLocaleDateString());
            const temperatures = vitals.map(v => parseFloat(v.temperature) || null);
            const heartRates = vitals.map(v => parseInt(v.heart_rate) || null);
            const systolicBP = vitals.map(v => parseInt(v.systolic_pressure || v.systolic_bp) || null);
            const diastolicBP = vitals.map(v => parseInt(v.diastolic_pressure || v.diastolic_bp) || null);
            const oxygenSat = vitals.map(v => parseFloat(v.oxygen_saturation) || null);

            // Destroy existing chart if it exists
            if (vitalSignsChart) {
                vitalSignsChart.destroy();
            }

            vitalSignsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Temperature (°C)',
                            data: temperatures,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4,
                            yAxisID: 'temp'
                        },
                        {
                            label: 'Heart Rate (bpm)',
                            data: heartRates,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            yAxisID: 'hr'
                        },
                        {
                            label: 'Systolic BP (mmHg)',
                            data: systolicBP,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            yAxisID: 'bp'
                        },
                        {
                            label: 'Diastolic BP (mmHg)',
                            data: diastolicBP,
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            tension: 0.4,
                            yAxisID: 'bp'
                        },
                        {
                            label: 'Oxygen Saturation (%)',
                            data: oxygenSat,
                            borderColor: '#6f42c1',
                            backgroundColor: 'rgba(111, 66, 193, 0.1)',
                            tension: 0.4,
                            yAxisID: 'spo2'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Vital Signs Trend',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        temp: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Temperature (°C)'
                            },
                            min: 35,
                            max: 42,
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        hr: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Heart Rate (bpm)'
                            },
                            min: 40,
                            max: 150,
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        bp: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Blood Pressure (mmHg)'
                            },
                            min: 40,
                            max: 200,
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        spo2: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Oxygen Saturation (%)'
                            },
                            min: 85,
                            max: 100,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Show Graph Error
        function showGraphError(message) {
            const graphContainer = document.getElementById('vitalSignsGraphContainer');
            graphContainer.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <button onclick="goBackToVitalSignsSelection()" 
                        style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <h6 style="color: #333; font-size: 18px; margin: 0;"><i class="fas fa-chart-line text-success"></i> Vital Signs Trend</h6>
                    <div></div>
                </div>
                <div style="text-align: center; padding: 60px 20px; color: #666;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 20px; color: #ffc107;"></i>
                    <h6 style="margin-bottom: 15px; color: #333;">${message}</h6>
                    <div style="display: flex; justify-content: center; gap: 10px;">
                        <button onclick="loadVitalSignsGraph()" style="background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-redo"></i> Try Again
                        </button>
                        <button onclick="goBackToVitalSignsSelection()" style="background: #6c757d; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Back to Selection
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Close functions for other modals
        function closeMedicalTeamModal() {
            document.getElementById('medicalTeamModal').style.display = 'none';
        }
        
        function closeEnvironmentalModal() {
            document.getElementById('environmentalModal').style.display = 'none';
        }
        
        function closeSurveyModal() {
            document.getElementById('surveyModal').style.display = 'none';
        }
        
        function closeAdmissionModal() {
            document.getElementById('admissionModal').style.display = 'none';
        }
        
        function closeFoodOrderingModal() {
            document.getElementById('foodOrderingModal').style.display = 'none';
        }

        function closeVitalSignsModal() {
            document.getElementById('vitalSignsModal').style.display = 'none';
            
            // Reset modal to initial state
            resetVitalSignsModal();
        }

        // Reset vital signs modal to initial selection state
        function resetVitalSignsModal() {
            // Hide all containers
            const listContainer = document.getElementById('vitalSignsListContainer');
            const graphContainer = document.getElementById('vitalSignsGraphContainer');
            const selectionScreen = document.getElementById('vitalSignsViewSelection');
            
            if (listContainer) {
                listContainer.style.display = 'none';
                listContainer.style.opacity = '0';
            }
            
            if (graphContainer) {
                graphContainer.style.display = 'none';
                graphContainer.style.opacity = '0';
            }
            
            // Show and reset selection screen
            if (selectionScreen) {
                selectionScreen.style.display = 'flex';
                selectionScreen.style.opacity = '1';
                selectionScreen.style.transform = 'translateY(0px)';
            }
            
            // Reset any chart instance
            if (vitalSignsChart) {
                vitalSignsChart.destroy();
                vitalSignsChart = null;
            }
            
            // Clear any content from previous views
            const tableContent = document.getElementById('vital-signs-table-content');
            const graphContent = document.getElementById('vital-graph-content');
            
            if (tableContent) {
                tableContent.innerHTML = '';
                tableContent.style.display = 'none';
            }
            
            if (graphContent) {
                graphContent.innerHTML = '<canvas id="vitalSignsChart" style="max-height: 400px; width: 100%;"></canvas>';
                graphContent.style.display = 'none';
            }
            
            // Reset loading states
            const listLoading = document.getElementById('vital-loading-list');
            const graphLoading = document.getElementById('vital-loading-graph');
            
            if (listLoading) {
                listLoading.style.display = 'block';
            }
            
            if (graphLoading) {
                graphLoading.style.display = 'block';
            }
        }

        // Go back to vital signs selection screen
        function goBackToVitalSignsSelection() {
            // Hide current containers
            const listContainer = document.getElementById('vitalSignsListContainer');
            const graphContainer = document.getElementById('vitalSignsGraphContainer');
            
            listContainer.style.opacity = '0';
            graphContainer.style.opacity = '0';
            
            setTimeout(() => {
                listContainer.style.display = 'none';
                graphContainer.style.display = 'none';
                
                // Show selection screen
                const selectionScreen = document.getElementById('vitalSignsViewSelection');
                selectionScreen.style.display = 'flex';
                selectionScreen.style.opacity = '0';
                selectionScreen.style.transform = 'translateY(-20px)';
                
                // Smooth fade in
                setTimeout(() => {
                    selectionScreen.style.opacity = '1';
                    selectionScreen.style.transform = 'translateY(0px)';
                }, 10);
                
                // Reset any chart instance
                if (vitalSignsChart) {
                    vitalSignsChart.destroy();
                    vitalSignsChart = null;
                }
            }, 200);
        }

        function closeHealthEducationModal() {
            document.getElementById('healthEducationModal').style.display = 'none';
        }

        function closeIntroductionModal() {
            document.getElementById('introductionModal').style.display = 'none';
        }
        
        function openVitalSignsModal() {
            document.getElementById('vitalSignsModal').style.display = 'flex';
            
            // Reset modal to initial state first
            resetVitalSignsModal();
        }

        // Step 2: Show vital signs list view with smooth transition
        function showVitalSignsList() {
            // Hide selection screen
            const selectionScreen = document.getElementById('vitalSignsViewSelection');
            selectionScreen.style.opacity = '0';
            selectionScreen.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                selectionScreen.style.display = 'none';
                
                // Show list container
                const listContainer = document.getElementById('vitalSignsListContainer');
                listContainer.style.display = 'block';
                listContainer.style.opacity = '0';
                
                // Smooth fade in
                setTimeout(() => {
                    listContainer.style.opacity = '1';
                }, 10);
                
                // Now load the data
                loadVitalSignsList();
            }, 200);
        }

        // Step 2: Show vital signs trend/graph view with smooth transition
        function showVitalSignsTrend() {
            // Hide selection screen
            const selectionScreen = document.getElementById('vitalSignsViewSelection');
            selectionScreen.style.opacity = '0';
            selectionScreen.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                selectionScreen.style.display = 'none';
                
                // Show graph container
                const graphContainer = document.getElementById('vitalSignsGraphContainer');
                graphContainer.style.display = 'block';
                graphContainer.style.opacity = '0';
                
                // Smooth fade in
                setTimeout(() => {
                    graphContainer.style.opacity = '1';
                }, 10);
                
                // Now load the graph data
                loadVitalSignsGraph();
            }, 200);
        }

        // Load vital signs list data (separate from graph)
        function loadVitalSignsList() {
            // Show loading state
            document.getElementById('vital-loading-list').style.display = 'block';
            document.getElementById('vital-signs-table-content').style.display = 'none';
            
            // Simulate a delay to show loading (in a real app, this would be a fetch request)
            setTimeout(() => {
                // Hide loading
                document.getElementById('vital-loading-list').style.display = 'none';
                
                // Check if patient has vital signs
                @if(isset($patient) && method_exists($patient, 'vitalSigns') && $patient->vitalSigns()->count() > 0)
                    // Get vital signs data
                    const vitalSigns = @json($patient->vitalSigns()->with('recorder')->latest('recorded_at')->get());
                    
                    // Show data container
                    document.getElementById('vital-signs-table-content').style.display = 'block';
                    
                    // Render the vital signs in list format
                    renderVitalSignsList(vitalSigns);
                @else
                    // Show empty message
                    document.getElementById('vital-signs-table-content').style.display = 'block';
                    document.getElementById('vital-signs-table-content').innerHTML = `
                        <div style="text-align: center; padding: 60px 20px; color: #666;">
                            <i class="fas fa-heartbeat fa-3x" style="margin-bottom: 20px; color: #ddd;"></i>
                            <h6 style="margin-bottom: 15px; color: #333;">No vital signs recorded yet</h6>
                            <p style="color: #666; font-size: 14px;">Vital signs will appear here once they are recorded.</p>
                        </div>
                    `;
                @endif
            }, 500);
        }
        
        // Render vital signs data in list/table format
        function renderVitalSignsList(vitalSigns) {
            // Sort vital signs by recorded_at in descending order (most recent first)
            vitalSigns.sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at));
            
            let html = `
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <thead style="background: linear-gradient(135deg, #00a99d, #007b7a); color: white;">
                            <tr>
                                <th style="padding: 12px 8px; text-align: left; font-weight: 600; font-size: 13px;">Date & Time</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">Temp (°C)</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">HR (bpm)</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">RR (bpm)</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">BP (mmHg)</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">SpO2 (%)</th>
                                <th style="padding: 12px 8px; text-align: center; font-weight: 600; font-size: 13px;">AVPU</th>
                                <th style="padding: 12px 8px; text-align: left; font-weight: 600; font-size: 13px;">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            vitalSigns.forEach((vital, index) => {
                // Format date
                const recordedDate = new Date(vital.recorded_at);
                const formattedDate = recordedDate.toLocaleString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                
                // Helper function to get vital status class and format value
                function getVitalCell(value, type, unit = '') {
                    if (!value || value === null || value === undefined) return '<span style="color: #999;">-</span>';
                    
                    const val = parseFloat(value);
                    let statusClass = '';
                    
                    switch(type) {
                        case 'temperature':
                            if (val < 35 || val > 39) statusClass = 'critical';
                            else if (val < 36 || val > 38) statusClass = 'warning';
                            else statusClass = 'normal';
                            break;
                        case 'heart_rate':
                            if (val < 50 || val > 120) statusClass = 'critical';
                            else if (val < 60 || val > 100) statusClass = 'warning';
                            else statusClass = 'normal';
                            break;
                        case 'respiratory_rate':
                            if (val < 10 || val > 25) statusClass = 'critical';
                            else if (val < 12 || val > 20) statusClass = 'warning';
                            else statusClass = 'normal';
                            break;
                        case 'systolic_bp':
                            if (val < 90 || val > 180) statusClass = 'critical';
                            else if (val < 100 || val > 140) statusClass = 'warning';
                            else statusClass = 'normal';
                            break;
                        case 'oxygen_saturation':
                            if (val < 90) statusClass = 'critical';
                            else if (val < 95) statusClass = 'warning';
                            else statusClass = 'normal';
                            break;
                    }
                    
                    const colors = {
                        'critical': '#dc3545',
                        'warning': '#fd7e14', 
                        'normal': '#28a745'
                    };
                    
                    const color = colors[statusClass] || '#333';
                    return `<span style="color: ${color}; font-weight: 500;">${value}${unit}</span>`;
                }
                
                // Get consciousness level display
                function getConsciousnessDisplay(level) {
                    if (!level) return '<span style="color: #999;">-</span>';
                    const levels = {
                        'A': 'Alert',
                        'V': 'Verbal', 
                        'P': 'Pain',
                        'U': 'Unresponsive'
                    };
                    const display = levels[level] || level;
                    const colors = {'A': '#28a745', 'V': '#ffc107', 'P': '#fd7e14', 'U': '#dc3545'};
                    const color = colors[level] || '#333';
                    return `<span style="color: ${color}; font-weight: 500;">${display}</span>`;
                }
                
                // Blood pressure formatting
                const bpValue = vital.systolic_bp && vital.diastolic_bp ? 
                    `${vital.systolic_bp}/${vital.diastolic_bp}` : 
                    (vital.systolic_bp ? vital.systolic_bp : null);
                
                const rowStyle = index === 0 ? 'background: linear-gradient(135deg, #e8f5f0, #f0f9f6);' : 
                               index % 2 === 0 ? 'background: #f8f9fa;' : 'background: white;';
                
                html += `
                    <tr style="${rowStyle} border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 8px; font-size: 13px;">
                            <div style="font-weight: 500; color: #333;">${formattedDate}</div>
                            ${index === 0 ? '<div style="font-size: 11px; color: #00a99d; font-weight: 600;">LATEST</div>' : ''}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${getVitalCell(vital.temperature, 'temperature', '°C')}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${getVitalCell(vital.heart_rate, 'heart_rate', ' bpm')}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${getVitalCell(vital.respiratory_rate, 'respiratory_rate', ' bpm')}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${bpValue ? getVitalCell(vital.systolic_bp, 'systolic_bp', bpValue.includes('/') ? '' : ' mmHg').replace(vital.systolic_bp, bpValue) : '<span style="color: #999;">-</span>'}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${getVitalCell(vital.oxygen_saturation, 'oxygen_saturation', '%')}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; font-size: 14px;">
                            ${getConsciousnessDisplay(vital.consciousness)}
                        </td>
                        <td style="padding: 12px 8px; font-size: 13px; color: #666;">
                            <i class="fas fa-user-nurse" style="margin-right: 5px; color: #00a99d;"></i>
                            ${vital.recorder ? vital.recorder.name : 'Unknown Staff'}
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 13px; color: #666;">
                    <strong>Total Records:</strong> ${vitalSigns.length} | 
                    <strong>Latest Record:</strong> ${new Date(vitalSigns[0].recorded_at).toLocaleDateString()}
                </div>
            `;
            
            document.getElementById('vital-signs-table-content').innerHTML = html;
        }
        
        // Environmental controls functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Temperature controls
            const decreaseTempBtn = document.getElementById('decreaseTemp');
            const increaseTempBtn = document.getElementById('increaseTemp');
            const tempDisplay = document.getElementById('current-temp');
            
            if (decreaseTempBtn && increaseTempBtn && tempDisplay) {
                decreaseTempBtn.addEventListener('click', function() {
                    let currentTemp = parseInt(tempDisplay.textContent);
                    if (currentTemp > 18) {
                        currentTemp--;
                        tempDisplay.textContent = currentTemp;
                        showToast(`Temperature set to ${currentTemp}°C`);
                    }
                });
                
                increaseTempBtn.addEventListener('click', function() {
                    let currentTemp = parseInt(tempDisplay.textContent);
                    if (currentTemp < 30) {
                        currentTemp++;
                        tempDisplay.textContent = currentTemp;
                        showToast(`Temperature set to ${currentTemp}°C`);
                    }
                });
            }
            
            // Lighting controls
            const lightButtons = document.querySelectorAll('.light-btn');
            if (lightButtons.length > 0) {
                lightButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove active class from all buttons
                        lightButtons.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');
                        showToast(`Lighting set to ${this.querySelector('span').textContent}`);
                    });
                });
            }
            
            // Curtain controls
            const curtainButtons = document.querySelectorAll('.curtain-btn');
            if (curtainButtons.length > 0) {
                curtainButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove active class from all buttons
                        curtainButtons.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        this.classList.add('active');
                        showToast(`Curtains ${this.querySelector('span').textContent}`);
                    });
                });
            }
            
            // Nurse call button in environmental controls
            const nurseCallBtn = document.getElementById('nurseCallBtn');
            if (nurseCallBtn) {
                nurseCallBtn.addEventListener('click', function() {
                    // Show the alert nurse modal
                    document.getElementById('environmentalModal').style.display = 'none';
                    document.getElementById('alertModal').style.display = 'flex';
                });
            }
            
            // Meal tab functionality
            const mealTabs = document.querySelectorAll('.meal-tab');
            if (mealTabs.length > 0) {
                mealTabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        // Remove active class from all tabs
                        mealTabs.forEach(t => t.classList.remove('active'));
                        // Add active class to clicked tab
                        this.classList.add('active');
                        
                        // Hide all meal option sections
                        const mealSections = document.querySelectorAll('.meal-options');
                        mealSections.forEach(section => {
                            section.style.display = 'none';
                        });
                        
                        // Show the selected meal section
                        const mealType = this.getAttribute('data-meal');
                        const selectedSection = document.getElementById(`${mealType}-options`);
                        if (selectedSection) {
                            selectedSection.style.display = 'block';
                        }
                    });
                });
            }
            
            // Food order button functionality
            const orderButtons = document.querySelectorAll('.order-btn');
            if (orderButtons.length > 0) {
                // Function to update the meal summary
                function updateMealSummary(mealType, itemName) {
                    const summaryElement = document.getElementById(`${mealType.toLowerCase()}-summary`);
                    if (summaryElement) {
                        const choiceElement = summaryElement.querySelector('.meal-choice');
                        if (choiceElement) {
                            choiceElement.textContent = itemName || 'None selected';
                            choiceElement.style.color = itemName ? '#28a745' : '#6c757d';
                        }
                    }
                }
                
                // Update the selected date in the summary
                function updateSelectedDate() {
                    const dateSelect = document.getElementById('orderDate');
                    const selectedDateElement = document.getElementById('selected-date');
                    
                    if (dateSelect && selectedDateElement) {
                        const selectedOption = dateSelect.options[dateSelect.selectedIndex];
                        selectedDateElement.textContent = selectedOption.text.replace('(', '').replace(')', '');
                    }
                }
                
                // Initialize the date display
                updateSelectedDate();
                
                // Add event listener to date select
                const dateSelect = document.getElementById('orderDate');
                if (dateSelect) {
                    dateSelect.addEventListener('change', updateSelectedDate);
                }
                
                orderButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const mealType = this.getAttribute('data-meal');
                        const itemName = this.getAttribute('data-item');
                        const dateSelect = document.getElementById('orderDate');
                        const dietaryRestriction = document.getElementById('dietaryRestrictions') ? 
                            document.getElementById('dietaryRestrictions').value : '';
                        
                        // Set selected status for the button
                        orderButtons.forEach(b => {
                            if (b.getAttribute('data-meal') === mealType) {
                                b.classList.remove('selected');
                                b.textContent = 'Order';
                            }
                        });
                        this.classList.add('selected');
                        this.textContent = 'Selected';
                        
                        // Update the meal summary display
                        updateMealSummary(mealType, itemName);
                        
                        // Show confirmation toast
                        let message = `${itemName} selected for ${mealType}`;
                        if (dateSelect) {
                            const selectedOption = dateSelect.options[dateSelect.selectedIndex];
                            message += ` on ${selectedOption.text.replace('(', '').replace(')', '')}`;
                        }
                        if (dietaryRestriction) {
                            message += ` (${dietaryRestriction})`;
                        }
                        showToast(message);
                    });
                });
                
                // Handle submit order button click
                const submitOrderBtn = document.getElementById('submit-order-btn');
                if (submitOrderBtn) {
                    submitOrderBtn.addEventListener('click', function() {
                        // Get all selected meals
                        const mealTypes = ['Breakfast', 'Lunch', 'Dinner', 'Snack'];
                        const selections = {};
                        let hasSelections = false;
                        
                        mealTypes.forEach(mealType => {
                            const selected = document.querySelector(`.order-btn[data-meal="${mealType}"].selected`);
                            if (selected) {
                                hasSelections = true;
                                selections[mealType] = {
                                    item: selected.getAttribute('data-item'),
                                    mealType
                                };
                            }
                        });
                        
                        if (!hasSelections) {
                            showToast('Please select at least one meal before submitting');
                            return;
                        }
                        
                        // Get order info
                        const dietaryRestriction = document.getElementById('dietaryRestrictions')?.value || '';
                        const dateSelect = document.getElementById('orderDate');
                        const orderDate = dateSelect?.value || '';
                        
                        // Get formatted date text for display
                        let dateText = '';
                        if (dateSelect) {
                            const selectedOption = dateSelect.options[dateSelect.selectedIndex];
                            dateText = selectedOption.text.replace('(', '').replace(')', '');
                        }
                        
                        // Disable the submit button and show loading state
                        submitOrderBtn.disabled = true;
                        submitOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                        
                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        // Submit each selected meal order via AJAX
                        let submittedOrders = 0;
                        let totalOrders = Object.keys(selections).length;
                        let hasErrors = false;
                        
                        Object.keys(selections).forEach(async (mealType) => {
                            const selection = selections[mealType];
                            
                            try {
                                const response = await fetch('{{ route("admin.patients.food-order.store", $patient->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        item_name: selection.item,
                                        meal_type: mealType,
                                        dietary_restriction: dietaryRestriction
                                    })
                                });
                                
                                const data = await response.json();
                                
                                if (response.ok && data.success) {
                                    // Order submitted successfully
                                    const ordersList = document.getElementById('orders-list');
                                    if (ordersList) {
                                        // Hide "no orders" message if present
                                        const noOrders = ordersList.querySelector('.no-orders');
                                        if (noOrders) {
                                            noOrders.style.display = 'none';
                                        }
                                        
                                        // Get the heading element (for inserting after it)
                                        const heading = ordersList.querySelector('h6');
                                        
                                        // Add the new order to the list
                                        const orderItem = document.createElement('div');
                                        orderItem.className = 'order-item';
                                        orderItem.id = `order-${data.order.id}`;
                                        orderItem.innerHTML = `
                                            <div class="order-info">
                                                <div class="order-name">${selection.item}</div>
                                                <div class="order-meal">${mealType} ${dietaryRestriction ? `(${dietaryRestriction})` : ''}</div>
                                                <div class="order-time">
                                                    <div><strong>Ordered:</strong> Just now</div>
                                                </div>
                                                <div class="order-status status-pending">Pending</div>
                                            </div>
                                            <button class="cancel-order" data-order-id="${data.order.id}">Cancel</button>
                                        `;
                                        
                                        // Insert after the heading
                                        if (heading && heading.nextSibling) {
                                            ordersList.insertBefore(orderItem, heading.nextSibling);
                                        } else {
                                            ordersList.appendChild(orderItem);
                                        }
                                        
                                        // Add event listener for the new cancel button
                                        const cancelBtn = orderItem.querySelector('.cancel-order');
                                        if (cancelBtn) {
                                            cancelBtn.addEventListener('click', function() {
                                                cancelOrder(data.order.id);
                                            });
                                        }
                                    }
                                } else {
                                    hasErrors = true;
                                    showToast(`Error submitting ${mealType} order: ${data.message || 'Unknown error'}`);
                                }
                                
                            } catch (error) {
                                hasErrors = true;
                                showToast(`Error submitting ${mealType} order: ${error.message}`);
                            }
                            
                            submittedOrders++;
                            
                            // Check if all orders have been processed
                            if (submittedOrders === totalOrders) {
                                // Re-enable the submit button
                                submitOrderBtn.disabled = false;
                                submitOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Order';
                                
                                // Clear selections and reset form
                                orderButtons.forEach(btn => {
                                    btn.classList.remove('selected');
                                    btn.textContent = 'Order';
                                });
                                
                                // Reset meal summaries
                                ['breakfast', 'lunch', 'dinner', 'snack'].forEach(meal => {
                                    updateMealSummary(meal.charAt(0).toUpperCase() + meal.slice(1), '');
                                });
                                
                                if (!hasErrors) {
                                    showToast(`Your meal orders for ${dateText} have been submitted successfully!`);
                                }
                            }
                        });
                    });
                }
            }
        });

        // Handle satisfaction survey submission
        document.addEventListener('DOMContentLoaded', function() {
            const satisfactionForm = document.getElementById('satisfactionForm');
            if (satisfactionForm) {
                satisfactionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get all the rating values
                    const careRating = document.querySelector('input[name="care_rating"]:checked')?.value || 0;
                    const staffRating = document.querySelector('input[name="staff_rating"]:checked')?.value || 0;
                    const cleanRating = document.querySelector('input[name="clean_rating"]:checked')?.value || 0;
                    const commRating = document.querySelector('input[name="comm_rating"]:checked')?.value || 0;
                    const comments = document.getElementById('comments')?.value || '';
                    
                    // Show thank you message
                    showToast('Thank you for your feedback!');
                    
                    // Close the survey modal
                    closeSurveyModal();
                    
                    // Reset the form
                    satisfactionForm.reset();
                });
            }
        });

        // Toast notification function
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // Show the toast
            setTimeout(function() {
                toast.classList.add('show');
            }, 100);
            
            // Hide the toast after 3 seconds
            setTimeout(function() {
                toast.classList.remove('show');
                setTimeout(function() {
                    document.body.removeChild(toast);
                }, 500);
            }, 3000);
        }
        
        // Cancel food order function
        async function cancelOrder(orderId) {
            const orderElement = document.getElementById(`order-${orderId}`);
            if (!orderElement) return;
            
            const cancelBtn = orderElement.querySelector('.cancel-order');
            if (cancelBtn) {
                cancelBtn.disabled = true;
                cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`{{ url('admin/food-order') }}/${orderId}/cancel`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Remove the order from the list
                    orderElement.remove();
                    showToast(data.message || 'Order cancelled successfully');
                    
                    // Check if there are no orders left and show "no orders" message
                    const ordersList = document.getElementById('orders-list');
                    const remainingOrders = ordersList.querySelectorAll('.order-item');
                    if (remainingOrders.length === 0) {
                        const noOrdersDiv = document.createElement('div');
                        noOrdersDiv.className = 'no-orders';
                        noOrdersDiv.textContent = 'No meals ordered yet';
                        ordersList.appendChild(noOrdersDiv);
                    }
                } else {
                    showToast(data.message || 'Failed to cancel order');
                    if (cancelBtn) {
                        cancelBtn.disabled = false;
                        cancelBtn.innerHTML = 'Cancel';
                    }
                }
            } catch (error) {
                showToast('Error cancelling order: ' + error.message);
                if (cancelBtn) {
                    cancelBtn.disabled = false;
                    cancelBtn.innerHTML = 'Cancel';
                }
            }
        }
        
        // Initialize existing cancel order buttons
        document.addEventListener('DOMContentLoaded', function() {
            const existingCancelButtons = document.querySelectorAll('.cancel-order');
            existingCancelButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    if (orderId) {
                        cancelOrder(orderId);
                    }
                });
            });
        });
        
        // Close medical information modal
        function closeMedicalInfoModal() {
            document.getElementById('medicalInfoModal').style.display = 'none';
        }
        
        // Medical information modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const medicalInfoModal = document.getElementById('medicalInfoModal');
            
            // Click outside to close modal
            if (medicalInfoModal) {
                medicalInfoModal.addEventListener('click', function(e) {
                    if (e.target === medicalInfoModal) {
                        closeMedicalInfoModal();
                    }
                });
                
                // Prevent modal from closing when clicking on content
                const medicalInfoContent = medicalInfoModal.querySelector('.medical-info-content');
                if (medicalInfoContent) {
                    medicalInfoContent.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            }
            
            // Medical history tab functionality
            const historyTabs = document.querySelectorAll('.history-tab');
            const historyItems = document.querySelectorAll('.history-item');
            
            if (historyTabs.length > 0) {
                historyTabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        // Remove active class from all tabs
                        historyTabs.forEach(t => t.classList.remove('active'));
                        // Add active class to clicked tab
                        this.classList.add('active');
                        
                        const filterType = this.getAttribute('data-type');
                        
                        // Show/hide history items based on selected type
                        historyItems.forEach(item => {
                            if (filterType === 'all' || item.getAttribute('data-type') === filterType) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                });
            }
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (medicalInfoModal && medicalInfoModal.style.display === 'flex') {
                        closeMedicalInfoModal();
                    }
                }
            });
        });
        
        // Send alert to nursing station
        function sendAlert(alertType) {
            const alertStatus = document.getElementById('alertStatus');
            
            // Show loading state
            alertStatus.style.display = 'block';
            alertStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending alert...';
            alertStatus.className = 'alert-status mt-3';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Send alert to server
            fetch('{{ route("admin.patients.alert.send", $patient->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    alert_type: alertType,
                    is_urgent: alertType === 'emergency'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alertStatus.innerHTML = '<i class="fas fa-check-circle text-success"></i> Alert sent successfully! Nursing station has been notified.';
                    alertStatus.className = 'alert-status mt-3 text-success';
                    
                    // Show toast notification
                    showToast('Alert sent to nursing station');
                    
                    // Auto-close modal after 2 seconds
                    setTimeout(() => {
                        closeAlertModal();
                    }, 2000);
                } else {
                    // Show error message
                    alertStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Failed to send alert. Please try again.';
                    alertStatus.className = 'alert-status mt-3 text-danger';
                }
            })
            .catch(error => {
                console.error('Error sending alert:', error);
                alertStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Network error. Please check your connection and try again.';
                alertStatus.className = 'alert-status mt-3 text-danger';
            });
        }
    </script>
    
    <!-- Vital Signs Modal -->
    <div class="vital-signs-modal" id="vitalSignsModal" style="z-index: 2000; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;">
        <div class="vital-signs-content" style="background-color: white; border-radius: 8px; width: 95%; max-width: 800px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); max-height: 85vh; overflow-y: auto;">
            <div class="vital-signs-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background-color: #00a99d; color: white; border-top-left-radius: 8px; border-top-right-radius: 8px; position: sticky; top: 0; z-index: 10;">
                <h5 style="margin: 0; font-size: 18px;">Vital Signs Information</h5>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="close-vital-signs-modal" style="cursor: pointer; font-size: 24px;">&times;</span>
                </div>
            </div>
            <div class="vital-signs-body" style="padding: 20px;">
                                  <!-- Step 1: Clean View Selection Screen (No data loading yet) -->
                  <div id="vitalSignsViewSelection" style="text-align: center; padding: 60px 30px; height: 100%; display: flex; flex-direction: column; justify-content: center; opacity: 1; transform: translateY(0px); transition: all 0.3s ease;">
                      <h6 style="margin-bottom: 50px; color: #333; font-size: 20px; font-weight: 600;">Choose how you want to view vital signs:</h6>
                      
                      <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">
                          <button onclick="showVitalSignsList()" 
                              style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border: none; padding: 30px 40px; border-radius: 15px; cursor: pointer; min-width: 200px; box-shadow: 0 6px 20px rgba(0,123,255,0.3); transition: all 0.4s ease;"
                              onmouseover="this.style.transform='translateY(-4px) scale(1.02)'; this.style.boxShadow='0 10px 25px rgba(0,123,255,0.4)'"
                              onmouseout="this.style.transform='translateY(0px) scale(1)'; this.style.boxShadow='0 6px 20px rgba(0,123,255,0.3)'">
                              <i class="fas fa-list" style="font-size: 32px; margin-bottom: 15px; display: block;"></i>
                              <div style="font-weight: bold; font-size: 18px; margin-bottom: 8px;">View List</div>
                              <div style="font-size: 13px; opacity: 0.9; line-height: 1.3;">Detailed table with all<br>vital signs records</div>
                          </button>
                          
                          <button onclick="showVitalSignsTrend()" 
                              style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; border: none; padding: 30px 40px; border-radius: 15px; cursor: pointer; min-width: 200px; box-shadow: 0 6px 20px rgba(40,167,69,0.3); transition: all 0.4s ease;"
                              onmouseover="this.style.transform='translateY(-4px) scale(1.02)'; this.style.boxShadow='0 10px 25px rgba(40,167,69,0.4)'"
                              onmouseout="this.style.transform='translateY(0px) scale(1)'; this.style.boxShadow='0 6px 20px rgba(40,167,69,0.3)'">
                              <i class="fas fa-chart-line" style="font-size: 32px; margin-bottom: 15px; display: block;"></i>
                              <div style="font-weight: bold; font-size: 18px; margin-bottom: 8px;">View Trend</div>
                              <div style="font-size: 13px; opacity: 0.9; line-height: 1.3;">Interactive graph showing<br>vital signs trends</div>
                          </button>
                      </div>
                  </div>

                  <!-- Step 2: List View Container (initially hidden, loads data only when selected) -->
                  <div id="vitalSignsListContainer" style="display: none; opacity: 0; transition: opacity 0.3s ease;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                          <button onclick="goBackToVitalSignsSelection()" 
                              style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                              <i class="fas fa-arrow-left"></i> Back
                          </button>
                          <h6 style="color: #333; font-size: 18px; margin: 0;"><i class="fas fa-list text-primary"></i> Vital Signs Records</h6>
                          <div></div>
                      </div>
                      
                      <div id="vital-loading-list" style="text-align: center; padding: 60px 20px;">
                          <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                          <p style="margin-top: 20px; color: #666; font-size: 16px;">Loading vital signs data...</p>
                      </div>
                      
                      <div id="vital-signs-table-content" style="display: none;">
                          <!-- Table content will be loaded here -->
                      </div>
                  </div>

                  <!-- Step 2: Graph View Container (initially hidden, loads data only when selected) -->
                  <div id="vitalSignsGraphContainer" style="display: none; opacity: 0; transition: opacity 0.3s ease;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                          <button onclick="goBackToVitalSignsSelection()" 
                              style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                              <i class="fas fa-arrow-left"></i> Back
                          </button>
                          <h6 style="color: #333; font-size: 18px; margin: 0;"><i class="fas fa-chart-line text-success"></i> Vital Signs Trend</h6>
                          <div></div>
                      </div>
                      
                      <div id="vital-loading-graph" style="text-align: center; padding: 60px 20px;">
                          <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                          <p style="margin-top: 20px; color: #666; font-size: 16px;">Loading trend data...</p>
                      </div>
                      
                      <div id="vital-graph-content" style="display: none; padding: 25px; background: #f8f9fa; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                          <canvas id="vitalSignsChart" style="max-height: 400px; width: 100%;"></canvas>
                      </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Information Modal -->
    <div class="medical-info-modal" id="medicalInfoModal">
        <div class="medical-info-content">
            <div class="medical-info-header">
                <h5>Medical Information</h5>
                <span class="close-medical-info-modal">&times;</span>
            </div>
            <div class="medical-info-body">
                <!-- Medications Section -->
                <div class="medical-section">
                    <h6 class="section-title">Current Medications</h6>
                    <div class="medications-list">
                        @forelse($medications as $medication)
                            <div class="medication-item {{ $medication->status }}">
                                <div class="medication-header">
                                    <div class="medication-name">{{ $medication->medication_name }}</div>
                                    <div class="medication-status status-{{ $medication->status }}">{{ ucfirst($medication->status) }}</div>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-row">
                                        <span class="label">Dosage:</span>
                                        <span class="value">{{ $medication->dosage }}</span>
                                    </div>
                                    <div class="medication-row">
                                        <span class="label">Frequency:</span>
                                        <span class="value">{{ $medication->frequency }}</span>
                                    </div>
                                    <div class="medication-row">
                                        <span class="label">Route:</span>
                                        <span class="value">{{ $medication->route }}</span>
                                    </div>
                                    @if($medication->instructions)
                                    <div class="medication-row">
                                        <span class="label">Instructions:</span>
                                        <span class="value">{{ $medication->instructions }}</span>
                                    </div>
                                    @endif
                                    <div class="medication-row">
                                        <span class="label">Prescribed by:</span>
                                        <span class="value">{{ $medication->prescribed_by }}</span>
                                    </div>
                                    <div class="medication-row">
                                        <span class="label">Start Date:</span>
                                        <span class="value">{{ $medication->start_date->format('d M Y') }}</span>
                                    </div>
                                    @if($medication->end_date)
                                    <div class="medication-row">
                                        <span class="label">End Date:</span>
                                        <span class="value">{{ $medication->end_date->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                    @if($medication->notes)
                                    <div class="medication-row">
                                        <span class="label">Notes:</span>
                                        <span class="value">{{ $medication->notes }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="no-medications">
                                <i class="fa fa-info-circle"></i>
                                <p>No medications recorded for this patient.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Medical History Section -->
                <div class="medical-section">
                    <h6 class="section-title">Medical History</h6>
                    <div class="medical-history-tabs">
                        <button class="history-tab active" data-type="all">All</button>
                        <button class="history-tab" data-type="condition">Conditions</button>
                        <button class="history-tab" data-type="allergy">Allergies</button>
                        <button class="history-tab" data-type="surgery">Surgeries</button>
                        <button class="history-tab" data-type="family_history">Family History</button>
                    </div>
                    <div class="medical-history-list">
                        @forelse($medicalHistories as $history)
                            <div class="history-item" data-type="{{ $history->type }}">
                                <div class="history-header">
                                    <div class="history-title">{{ $history->title }}</div>
                                    <div class="history-type type-{{ $history->type }}">{{ ucfirst(str_replace('_', ' ', $history->type)) }}</div>
                                </div>
                                <div class="history-details">
                                    @if($history->description)
                                    <div class="history-row">
                                        <span class="label">Description:</span>
                                        <span class="value">{{ $history->description }}</span>
                                    </div>
                                    @endif
                                    @if($history->date_diagnosed)
                                    <div class="history-row">
                                        <span class="label">Date Diagnosed:</span>
                                        <span class="value">{{ $history->date_diagnosed->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                    @if($history->severity)
                                    <div class="history-row">
                                        <span class="label">Severity:</span>
                                        <span class="value severity-{{ strtolower($history->severity) }}">{{ ucfirst($history->severity) }}</span>
                                    </div>
                                    @endif
                                    <div class="history-row">
                                        <span class="label">Status:</span>
                                        <span class="value status-{{ strtolower($history->status) }}">{{ ucfirst($history->status) }}</span>
                                    </div>
                                    @if($history->notes)
                                    <div class="history-row">
                                        <span class="label">Notes:</span>
                                        <span class="value">{{ $history->notes }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="no-history">
                                <i class="fa fa-info-circle"></i>
                                <p>No medical history recorded for this patient.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Environmental Control Modal -->
</body>
</html>