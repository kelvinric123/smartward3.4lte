<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultants - {{ $ward->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f9;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-header .badge {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: normal;
        }
        
        .consultant-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .consultant-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f3f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        
        .consultant-item:hover {
            background-color: #f8f9fa;
        }
        
        .consultant-item:last-child {
            border-bottom: none;
        }
        
        .consultant-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .consultant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .consultant-details h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .consultant-details p {
            margin: 2px 0 0 0;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .patient-count {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #e3f2fd;
            border-radius: 20px;
            color: #1976d2;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .patient-count i {
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-state h4 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
        }
        
        .empty-state p {
            margin: 0;
            font-size: 0.9rem;
        }
        
        .summary {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .summary h3 {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .consultant-name:hover {
            text-decoration: underline;
            color: #0056b3 !important;
        }
        
        /* Search Styles */
        .search-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            color: #6c757d;
            z-index: 2;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 45px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            outline: none;
        }
        
        .search-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .clear-search {
            position: absolute;
            right: 15px;
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.2s ease;
        }
        
        .clear-search:hover {
            background-color: #f8f9fa;
            color: #dc3545;
        }
        
        .search-stats {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Hidden class for search results */
        .consultant-hidden {
            display: none !important;
        }
        
        /* Section hidden when no results */
        .section-hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-user-md"></i> Consultants</h1>
        <p>{{ $ward->name }} - 
            @if($ward->specialties->count() > 0)
                {{ $ward->specialties->pluck('name')->join(', ') }} Ward
            @elseif($ward->specialty)
                {{ $ward->specialty->name }} Ward
            @else
                Ward
            @endif
        </p>
    </div>

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="consultantSearch" placeholder="Search consultants by name, specialty, or qualification..." class="search-input">
            <button type="button" id="clearSearch" class="clear-search" style="display: none;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="search-stats">
            <span id="searchResults">Showing all consultants</span>
        </div>
    </div>

    <!-- Specialty Consultants Section -->
    <div class="section">
        <div class="section-header">
            <span><i class="fas fa-stethoscope"></i> 
                @if($ward->specialties->count() > 0)
                    {{ $ward->specialties->pluck('name')->join(', ') }} Consultants
                @elseif($ward->specialty)
                    {{ $ward->specialty->name }} Consultants
                @else
                    Ward Consultants
                @endif
            </span>
            <span class="badge">{{ $specialtyConsultants->count() }}</span>
        </div>
        
        @if($specialtyConsultants->count() > 0)
            <ul class="consultant-list">
                @foreach($specialtyConsultants as $consultant)
                    <li class="consultant-item">
                        <div class="consultant-info">
                            <div class="consultant-avatar">
                                {{ strtoupper(substr($consultant->name, 0, 1)) }}
                            </div>
                            <div class="consultant-details">
                                <h4 class="consultant-name" data-consultant-id="{{ $consultant->id }}" style="cursor: pointer; color: #007bff;">{{ $consultant->name }}</h4>
                                <p>{{ $consultant->qualification }} • {{ $consultant->experience_years }} years</p>
                            </div>
                        </div>
                        <div class="patient-count">
                            <i class="fas fa-user-injured"></i>
                            {{ $consultant->patient_count }} patient{{ $consultant->patient_count != 1 ? 's' : '' }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-state">
                <i class="fas fa-user-md"></i>
                <h4>No {{ $ward->specialty->name }} Consultants</h4>
                <p>No active consultants found for this specialty.</p>
            </div>
        @endif
    </div>

    <!-- Other Specialties Consultants Section -->
    <div class="section">
        <div class="section-header">
            <span><i class="fas fa-stethoscope"></i> Other Specialties Consultants</span>
            <span class="badge">{{ $otherConsultants->count() }}</span>
        </div>
        
        @if($otherConsultants->count() > 0)
            <ul class="consultant-list">
                @foreach($otherConsultants as $consultant)
                    <li class="consultant-item">
                        <div class="consultant-info">
                            <div class="consultant-avatar">
                                {{ strtoupper(substr($consultant->name, 0, 1)) }}
                            </div>
                            <div class="consultant-details">
                                <h4 class="consultant-name" data-consultant-id="{{ $consultant->id }}" style="cursor: pointer; color: #007bff;">{{ $consultant->name }}</h4>
                                <p>{{ $consultant->specialty->name }} • {{ $consultant->qualification }}</p>
                            </div>
                        </div>
                        <div class="patient-count">
                            <i class="fas fa-user-injured"></i>
                            {{ $consultant->patient_count }} patient{{ $consultant->patient_count != 1 ? 's' : '' }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="empty-state">
                <i class="fas fa-stethoscope"></i>
                <h4>No Other Consultants</h4>
                <p>No other active consultants from different specialties.</p>
            </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <h3><i class="fas fa-chart-bar"></i> Summary</h3>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $specialtyConsultants->count() }}</div>
                <div class="stat-label">{{ $ward->specialty->name }} Consultants</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $otherConsultants->count() }}</div>
                <div class="stat-label">Other Specialties</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $specialtyConsultants->sum('patient_count') + $otherConsultants->sum('patient_count') }}</div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $specialtyConsultants->count() + $otherConsultants->count() }}</div>
                <div class="stat-label">Total Consultants</div>
            </div>
        </div>
    </div>
    
    <script>
        // Handle consultant name clicks
        document.addEventListener('DOMContentLoaded', function() {
            const consultantNames = document.querySelectorAll('.consultant-name');
            
            consultantNames.forEach(function(name) {
                name.addEventListener('click', function() {
                    const consultantId = this.getAttribute('data-consultant-id');
                    const consultantName = this.textContent.trim();
                    
                    // Send message to parent window to filter dashboard
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage({
                            type: 'filterByConsultant',
                            consultantId: consultantId,
                            consultantName: consultantName
                        }, '*');
                    }
                });
            });
            
            // Search functionality
            const searchInput = document.getElementById('consultantSearch');
            const clearSearchBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');
            const consultantItems = document.querySelectorAll('.consultant-item');
            const sections = document.querySelectorAll('.section');
            
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;
                let specialtyVisible = 0;
                let otherVisible = 0;
                
                consultantItems.forEach(function(item) {
                    const name = item.querySelector('.consultant-details h4').textContent.toLowerCase();
                    const details = item.querySelector('.consultant-details p').textContent.toLowerCase();
                    
                    if (searchTerm === '' || name.includes(searchTerm) || details.includes(searchTerm)) {
                        item.classList.remove('consultant-hidden');
                        visibleCount++;
                        
                        // Count visible items in each section
                        if (item.closest('.section').querySelector('.section-header').textContent.includes('Specialties')) {
                            otherVisible++;
                        } else {
                            specialtyVisible++;
                        }
                    } else {
                        item.classList.add('consultant-hidden');
                    }
                });
                
                // Show/hide sections based on results
                sections.forEach(function(section) {
                    const sectionItems = section.querySelectorAll('.consultant-item:not(.consultant-hidden)');
                    if (sectionItems.length === 0) {
                        section.classList.add('section-hidden');
                    } else {
                        section.classList.remove('section-hidden');
                    }
                });
                
                // Update search results text
                if (searchTerm === '') {
                    searchResults.textContent = 'Showing all consultants';
                } else {
                    searchResults.textContent = `Found ${visibleCount} consultant${visibleCount !== 1 ? 's' : ''} for "${searchTerm}"`;
                }
                
                // Show/hide clear button
                clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
            }
            
            // Search input event listener
            searchInput.addEventListener('input', performSearch);
            
            // Clear search button
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });
            
            // Keyboard shortcuts
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    performSearch();
                }
            });
        });
    </script>
</body>
</html> 