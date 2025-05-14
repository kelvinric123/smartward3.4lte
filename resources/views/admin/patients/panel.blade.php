<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QM Smart Ward Patient System</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <i class="fa fa-hospital-user"></i>
            QM Smart Ward Patient System
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
                <div class="panel-button">
                    <i class="fa fa-pills"></i>
                    <div>Medication Information</div>
                </div>
                <div class="panel-button">
                    <i class="fa fa-clipboard-list"></i>
                    <div>Treatment Plan</div>
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
        </div>
    </div>
    
    <!-- Alert Nurse Modal -->
    <div class="alert-nurse-modal" id="alertNurseModal">
        <div class="alert-nurse-content">
            <div class="alert-nurse-header">
                <h5>Alert Nurse</h5>
                <span class="close-modal">&times;</span>
            </div>
            <div class="alert-nurse-body">
                <div class="alert-buttons">
                    <button class="alert-btn emergency-btn" data-alert-type="emergency">Emergency</button>
                    <button class="alert-btn pain-btn" data-alert-type="pain">Pain</button>
                    <button class="alert-btn assistance-btn" data-alert-type="assistance">Need Assistance</button>
                    <button class="alert-btn water-btn" data-alert-type="water">Water</button>
                    <button class="alert-btn bathroom-btn" data-alert-type="bathroom">Bathroom</button>
                    <button class="alert-btn food-btn" data-alert-type="food">Food</button>
                </div>
                <iframe id="notification-iframe" style="display: none;" width="0" height="0" frameborder="0"></iframe>
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
    
    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-item">
            <i class="fa fa-info-circle"></i>
            <span>Introduction</span>
        </div>
        <div class="nav-item" id="admission-info-btn">
            <i class="fa fa-hospital-user"></i>
            <span>Admission Information</span>
        </div>
        <div class="nav-item">
            <i class="fa fa-heartbeat"></i>
            <span>Health Education</span>
        </div>
        <div class="nav-item">
            <i class="fa fa-chart-line"></i>
            <span>Vital Sign Info</span>
        </div>
    </div>
    
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
            
            // Update date in format: WED, 05/14/2024
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
            const day = days[now.getDay()];
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const date = String(now.getDate()).padStart(2, '0');
            const year = now.getFullYear();
            document.getElementById('current-date').textContent = `${day}, ${month}/${date}/${year}`;
        }
        
        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial call
        
        // YouTube video handling
        let player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-video', {
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange,
                    'onError': handleVideoError
                }
            });
        }

        function onPlayerReady(event) {
            event.target.playVideo();
        }

        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                event.target.playVideo();
            }
        }
        
        function handleVideoError() {
            const videoContainer = document.querySelector('.video-container');
            if (videoContainer) {
                videoContainer.innerHTML = '<div class="video-fallback">Video is temporarily unavailable</div>';
            }
        }
        
        // Load YouTube API
        let tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        let firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
        window.addEventListener('load', function() {
            // Fallback for direct iframe playing if API fails
            setTimeout(() => {
                if (!player || !player.playVideo) {
                    const iframe = document.getElementById('youtube-video');
                    if (iframe) {
                        iframe.src = iframe.src.replace('enablejsapi=1', 'enablejsapi=0');
                    }
                }
            }, 3000);
        });
        
        // Alert Nurse functionality
        const alertNurseBtn = document.getElementById('alert-nurse-btn');
        const alertNurseModal = document.getElementById('alertNurseModal');
        const closeModal = document.querySelector('.close-modal');
        const alertButtons = document.querySelectorAll('.alert-btn');
        const notificationIframe = document.getElementById('notification-iframe');
        
        // Show alert nurse modal
        alertNurseBtn.addEventListener('click', function() {
            alertNurseModal.style.display = 'flex';
        });
        
        // Close modal when clicking X
        closeModal.addEventListener('click', function() {
            alertNurseModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === alertNurseModal) {
                alertNurseModal.style.display = 'none';
            }
        });
        
        // Handle alert button clicks
        alertButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alertType = this.getAttribute('data-alert-type');
                const patientId = {{ $patient->id }};
                const patientName = "{{ $patient->name }}";
                const bedNumber = "{{ $bed->bed_number }}";
                
                // Show confirmation
                alert(`${alertType.charAt(0).toUpperCase() + alertType.slice(1)} alert sent for patient ${patientName} in bed ${bedNumber}`);
                
                // Send notification to dashboard via iframe
                notificationIframe.src = `http://localhost:8000/admin/beds/wards/1/dashboard?notification=true&type=${alertType}&patient=${patientId}&bed=${bedNumber}`;
                
                // Close the modal
                alertNurseModal.style.display = 'none';
            });
        });
        
        // Medical Team functionality
        const medicalTeamBtn = document.getElementById('medical-team-btn');
        const medicalTeamModal = document.getElementById('medicalTeamModal');
        const closeMedicalModal = document.querySelector('.close-medical-modal');
        
        // Show medical team modal
        medicalTeamBtn.addEventListener('click', function() {
            medicalTeamModal.style.display = 'flex';
        });
        
        // Close modal when clicking X
        closeMedicalModal.addEventListener('click', function() {
            medicalTeamModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === medicalTeamModal) {
                medicalTeamModal.style.display = 'none';
            }
        });
        
        // Button click handlers for other buttons
        document.querySelectorAll('.panel-button').forEach(button => {
            button.addEventListener('click', function() {
                // Skip buttons that have their own handlers
                if (this.id === 'alert-nurse-btn' || this.id === 'medical-team-btn') return;
                
                const buttonText = this.querySelector('div').textContent;
                console.log(`Button clicked: ${buttonText}`);
                // Link to appropriate page or show relevant modal when implemented
            });
        });
        
        // Navigation item click handlers - no alerts
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Skip buttons that have their own handlers
                if (this.id === 'admission-info-btn') return;
                
                const itemText = this.querySelector('span').textContent;
                console.log(`Navigation item clicked: ${itemText}`);
                // Link to appropriate page or show relevant modal when implemented
            });
        });
        
        // Admission Information functionality
        const admissionInfoBtn = document.getElementById('admission-info-btn');
        const admissionModal = document.getElementById('admissionModal');
        const closeAdmissionModal = document.querySelector('.close-admission-modal');
        
        // Show admission information modal
        admissionInfoBtn.addEventListener('click', function() {
            admissionModal.style.display = 'flex';
            
            // Calculate and update admission duration in real-time
            updateAdmissionDuration();
        });
        
        // Close modal when clicking X
        closeAdmissionModal.addEventListener('click', function() {
            admissionModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === admissionModal) {
                admissionModal.style.display = 'none';
            }
        });
        
        // Function to calculate and display admission duration
        function updateAdmissionDuration() {
            const admissionDate = new Date('{{ $activeAdmission->admission_date->toISOString() }}');
            const now = new Date();
            
            // Calculate difference in milliseconds
            const diff = now - admissionDate;
            
            // Calculate days, hours, minutes
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            // Format the duration string
            let durationStr = '';
            if (days > 0) {
                durationStr += `${days} day${days > 1 ? 's' : ''} `;
            }
            if (hours > 0 || days > 0) {
                durationStr += `${hours} hour${hours > 1 ? 's' : ''} `;
            }
            durationStr += `${minutes} minute${minutes > 1 ? 's' : ''}`;
            
            // Update the element
            const durationElement = document.getElementById('admissionDuration');
            if (durationElement) {
                durationElement.textContent = durationStr;
            }
        }
        
        // Environmental Control functionality
        const environmentalBtn = document.getElementById('environmental-control-btn');
        const environmentalModal = document.getElementById('environmentalModal');
        const closeEnvironmentalModal = document.querySelector('.close-environmental-modal');
        
        // Temperature control
        const currentTemp = document.getElementById('current-temp');
        const decreaseTemp = document.getElementById('decreaseTemp');
        const increaseTemp = document.getElementById('increaseTemp');
        
        // Lighting control
        const lightButtons = document.querySelectorAll('.light-btn');
        
        // Curtain control
        const curtainButtons = document.querySelectorAll('.curtain-btn');
        
        // Nurse call
        const nurseCallBtn = document.getElementById('nurseCallBtn');
        
        // Show environmental control modal
        environmentalBtn.addEventListener('click', function() {
            environmentalModal.style.display = 'flex';
        });
        
        // Close modal when clicking X
        closeEnvironmentalModal.addEventListener('click', function() {
            environmentalModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === environmentalModal) {
                environmentalModal.style.display = 'none';
            }
        });
        
        // Temperature control handlers
        decreaseTemp.addEventListener('click', function() {
            let temp = parseInt(currentTemp.textContent);
            if (temp > 18) {
                temp--;
                currentTemp.textContent = temp;
                showToast(`Temperature set to ${temp}°C`);
            }
        });
        
        increaseTemp.addEventListener('click', function() {
            let temp = parseInt(currentTemp.textContent);
            if (temp < 30) {
                temp++;
                currentTemp.textContent = temp;
                showToast(`Temperature set to ${temp}°C`);
            }
        });
        
        // Lighting control handlers
        lightButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                lightButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show toast message
                const mode = this.querySelector('span').textContent;
                showToast(`Lighting set to ${mode} mode`);
            });
        });
        
        // Curtain control handlers
        curtainButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                curtainButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show toast message
                const action = this.querySelector('span').textContent;
                showToast(`Curtains ${action.toLowerCase()}`);
            });
        });
        
        // Nurse call handler
        nurseCallBtn.addEventListener('click', function() {
            // Reuse the alert nurse functionality
            const patientId = {{ $patient->id }};
            const patientName = "{{ $patient->name }}";
            const bedNumber = "{{ $bed->bed_number }}";
            
            // Show confirmation
            showToast('Nurse has been called for assistance');
            
            // Send notification to dashboard via iframe
            notificationIframe.src = `http://localhost:8000/admin/beds/wards/1/dashboard?notification=true&type=assistance&patient=${patientId}&bed=${bedNumber}`;
            
            // Close the modal
            environmentalModal.style.display = 'none';
        });
        
        // Toast notification function
        function showToast(message) {
            // Create toast element if it doesn't exist
            let toast = document.getElementById('toast-notification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast-notification';
                document.body.appendChild(toast);
            }
            
            // Set message and show toast
            toast.textContent = message;
            toast.className = 'toast-notification show';
            
            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.className = 'toast-notification';
            }, 3000);
        }
        
        // Satisfaction Survey functionality
        const satisfactionBtn = document.getElementById('satisfaction-survey-btn');
        const surveyModal = document.getElementById('surveyModal');
        const closeSurveyModal = document.querySelector('.close-survey-modal');
        const satisfactionForm = document.getElementById('satisfactionForm');
        
        // Show satisfaction survey modal
        satisfactionBtn.addEventListener('click', function() {
            surveyModal.style.display = 'flex';
        });
        
        // Close modal when clicking X
        closeSurveyModal.addEventListener('click', function() {
            surveyModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === surveyModal) {
                surveyModal.style.display = 'none';
            }
        });
        
        // Form submission
        satisfactionForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form data
            const careRating = document.querySelector('input[name="care_rating"]:checked')?.value || '0';
            const staffRating = document.querySelector('input[name="staff_rating"]:checked')?.value || '0';
            const cleanRating = document.querySelector('input[name="clean_rating"]:checked')?.value || '0';
            const commRating = document.querySelector('input[name="comm_rating"]:checked')?.value || '0';
            const comments = document.getElementById('comments').value;
            
            // Create survey data
            const surveyData = {
                patient_id: {{ $patient->id }},
                care_rating: careRating,
                staff_rating: staffRating,
                clean_rating: cleanRating,
                comm_rating: commRating,
                comments: comments,
                submission_date: new Date().toISOString()
            };
            
            // Log the data (would normally be sent to server)
            console.log('Survey Submission:', surveyData);
            
            // Show success message
            showToast('Thank you for your feedback!');
            
            // Close the modal
            surveyModal.style.display = 'none';
            
            // Reset the form
            satisfactionForm.reset();
        });
        
        // Update the button click handler to skip all buttons that have their own functionality
        document.querySelectorAll('.panel-button').forEach(button => {
            button.addEventListener('click', function() {
                // Skip buttons that have their own handlers
                if (this.id === 'alert-nurse-btn' || this.id === 'medical-team-btn' || 
                    this.id === 'environmental-control-btn' || this.id === 'satisfaction-survey-btn') {
                    return;
                }
                
                // For other buttons, just get the text - no alert needed
                const buttonText = this.querySelector('div').textContent;
                console.log(`Button clicked: ${buttonText}`);
                // Link to appropriate page or show relevant modal when implemented
            });
        });
    </script>
</body>
</html> 