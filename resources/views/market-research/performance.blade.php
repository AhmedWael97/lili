@extends('layouts.app')

@section('title', 'Performance Dashboard')

@section('content')
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 mb-2">Algorithm Performance Dashboard</h1>
            <p class="lead text-muted">Track how user feedback improves data quality over time</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Overall Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body text-center py-4">
                    <div class="display-3 fw-bold mb-2">{{ number_format($dashboard['overall_improvement'], 1) }}%</div>
                    <div class="h5 mb-1">Overall Improvement</div>
                    <small class="opacity-75">Last 30 days</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="display-4 fw-bold text-primary mb-2">{{ number_format($dashboard['total_feedback_items']) }}</div>
                    <div class="h5 text-muted">Total Feedback Items</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="display-4 fw-bold text-success mb-2">{{ number_format($dashboard['feedback_last_30_days']) }}</div>
                    <div class="h5 text-muted">Recent Feedback</div>
                    <small class="text-muted">Last 30 days</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Component Performance -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h3 mb-4">
                <i class="bi bi-graph-up me-2"></i>
                Component Performance
            </h2>
        </div>

        @forelse($dashboard['component_performance'] as $component => $performance)
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h3 class="h5 mb-0">{{ ucwords(str_replace('_', ' ', $component)) }}</h3>
                    <span class="badge 
                        @if($performance['status'] === 'excellent') bg-success
                        @elseif($performance['status'] === 'good') bg-info
                        @elseif($performance['status'] === 'fair') bg-warning
                        @else bg-danger
                        @endif
                        px-3 py-2">
                        {{ strtoupper($performance['status']) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Accuracy -->
                        <div class="col-md-3 text-center">
                            <div class="mb-2 text-muted small fw-bold">ACCURACY</div>
                            <div class="display-6 fw-bold mb-3">{{ number_format($performance['accuracy'] * 100, 1) }}%</div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($performance['accuracy'] >= 0.9) bg-success
                                    @elseif($performance['accuracy'] >= 0.8) bg-info
                                    @elseif($performance['accuracy'] >= 0.7) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $performance['accuracy'] * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Precision -->
                        <div class="col-md-3 text-center">
                            <div class="mb-2 text-muted small fw-bold">PRECISION</div>
                            <div class="display-6 fw-bold mb-3">{{ number_format($performance['precision'] * 100, 1) }}%</div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($performance['precision'] >= 0.9) bg-success
                                    @elseif($performance['precision'] >= 0.8) bg-info
                                    @elseif($performance['precision'] >= 0.7) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $performance['precision'] * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Recall -->
                        <div class="col-md-3 text-center">
                            <div class="mb-2 text-muted small fw-bold">RECALL</div>
                            <div class="display-6 fw-bold mb-3">{{ number_format($performance['recall'] * 100, 1) }}%</div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($performance['recall'] >= 0.9) bg-success
                                    @elseif($performance['recall'] >= 0.8) bg-info
                                    @elseif($performance['recall'] >= 0.7) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $performance['recall'] * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- F1 Score -->
                        <div class="col-md-3 text-center">
                            <div class="mb-2 text-muted small fw-bold">F1 SCORE</div>
                            <div class="display-6 fw-bold mb-3">{{ number_format($performance['f1_score'] * 100, 1) }}%</div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($performance['f1_score'] >= 0.9) bg-success
                                    @elseif($performance['f1_score'] >= 0.8) bg-info
                                    @elseif($performance['f1_score'] >= 0.7) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    style="width: {{ $performance['f1_score'] * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3 text-muted small">
                        <i class="bi bi-database me-1"></i>
                        {{ number_format($performance['total_samples']) }} samples analyzed
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No performance data available yet. Start collecting feedback to see metrics.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Current Thresholds -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="h4 mb-1">
                        <i class="bi bi-sliders me-2"></i>
                        Current Quality Thresholds
                    </h3>
                    <p class="text-muted small mb-0">These thresholds are automatically adjusted based on user feedback</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($dashboard['current_thresholds'] as $key => $value)
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted fw-bold text-uppercase">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                </div>
                                <div class="display-6 fw-bold text-primary">{{ $value }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12 text-center">
            <form action="{{ route('feedback.train') }}" method="POST" class="d-inline-block me-3">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('This will re-train the algorithm with recent feedback. Continue?')">
                    <i class="bi bi-cpu me-2"></i>
                    Re-train Algorithm
                </button>
            </form>

            <a href="{{ route('feedback.performance') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Refresh Data
            </a>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection
