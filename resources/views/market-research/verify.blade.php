@extends('layouts.app')

@section('title', 'Verify Market Research Data')

@section('content')
<div class="container py-5">
    <div class="verification-header text-center mb-5">
        <h1 class="display-4 mb-3">Verify Competitors Data</h1>
        <p class="lead text-muted">AI has pre-screened the data - please verify the remaining competitors</p>
        
        <!-- AI Filter Stats -->
        @if(isset($competitors) && $competitors->isNotEmpty())
            @php
                $aiApproved = $competitors->filter(function($c) {
                    return isset($c->ai_filter) && isset($c->ai_filter['recommendation']) && $c->ai_filter['recommendation'] === 'approve';
                })->count();
                $aiReview = $competitors->filter(function($c) {
                    return !isset($c->ai_filter) || (isset($c->ai_filter['recommendation']) && $c->ai_filter['recommendation'] === 'review');
                })->count();
            @endphp
            
            @if($aiApproved > 0 || $aiReview > 0)
                <div class="alert alert-info mb-4">
                    <i class="bi bi-robot me-2"></i>
                    <strong>AI Pre-Filter:</strong> 
                    @if($aiApproved > 0)
                        <span class="badge bg-success me-2">{{ $aiApproved }} Auto-Approved</span>
                    @endif
                    @if($aiReview > 0)
                        <span class="badge bg-warning text-dark">{{ $aiReview }} Need Your Review</span>
                    @endif
                </div>
            @endif
        @endif
        
        <div class="progress mb-3" style="height: 30px;">
            <div class="progress-bar bg-success" role="progressbar" 
                 style="width: {{ ($verifiedCount / $totalCount) * 100 }}%"
                 id="verificationProgress">
                <span class="fw-bold">{{ $verifiedCount }} / {{ $totalCount }} Verified</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($competitors as $index => $competitor)
            <div class="col-12 mb-4">
                <div class="card shadow-sm competitor-card {{ $competitor->feedbacks->isNotEmpty() ? 'border-success' : '' }}" 
                     id="competitor-{{ $competitor->id }}">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-building me-2"></i>
                            {{ $competitor->business_name }}
                        </h3>
                        <div class="d-flex gap-2 align-items-center">
                            @if($competitor->feedbacks->isNotEmpty())
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Verified
                                </span>
                            @endif
                            
                            <!-- AI Filter Badge -->
                            @if(isset($competitor->ai_filter))
                                @php
                                    $aiFilter = is_array($competitor->ai_filter) ? $competitor->ai_filter : json_decode($competitor->ai_filter, true);
                                    $confidence = $aiFilter['confidence'] ?? 0;
                                    $recommendation = $aiFilter['recommendation'] ?? 'review';
                                @endphp
                                
                                @if($recommendation === 'approve')
                                    <span class="badge bg-success bg-opacity-75" title="AI Confidence: {{ $confidence }}%">
                                        <i class="bi bi-robot me-1"></i> AI: Approved
                                    </span>
                                @elseif($recommendation === 'reject')
                                    <span class="badge bg-danger bg-opacity-75" title="AI Confidence: {{ $confidence }}%">
                                        <i class="bi bi-robot me-1"></i> AI: Flagged
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark bg-opacity-75" title="AI Confidence: {{ $confidence }}%">
                                        <i class="bi bi-robot me-1"></i> AI: Review Needed
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Competitor Details -->
                        <div class="competitor-details mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Website:</strong><br>
                                    @if($competitor->website)
                                        <a href="{{ $competitor->website }}" target="_blank" class="text-decoration-none">
                                            {{ $competitor->website }}
                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>

                                @if($competitor->phone)
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Phone:</strong><br>
                                    <a href="tel:{{ $competitor->phone }}" class="text-decoration-none">
                                        {{ $competitor->phone }}
                                    </a>
                                </div>
                                @endif

                                @if($competitor->address)
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Address:</strong><br>
                                    {{ $competitor->address }}
                                </div>
                                @endif

                                @if($competitor->category)
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Category:</strong><br>
                                    <span class="badge bg-secondary">{{ $competitor->category }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Social Media Links -->
                            @if($competitor->facebook_handle || $competitor->instagram_handle || $competitor->twitter_handle || $competitor->linkedin_url)
                            <div class="social-media-section mt-3">
                                <strong class="text-muted d-block mb-2">Social Media:</strong>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($competitor->facebook_handle)
                                        <a href="https://facebook.com/{{ $competitor->facebook_handle }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-facebook me-1"></i> Facebook
                                        </a>
                                    @endif
                                    @if($competitor->instagram_handle)
                                        <a href="https://instagram.com/{{ $competitor->instagram_handle }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-instagram me-1"></i> Instagram
                                        </a>
                                    @endif
                                    @if($competitor->twitter_handle)
                                        <a href="https://twitter.com/{{ $competitor->twitter_handle }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-twitter me-1"></i> Twitter
                                        </a>
                                    @endif
                                    @if($competitor->linkedin_url)
                                        <a href="{{ $competitor->linkedin_url }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-linkedin me-1"></i> LinkedIn
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- AI Analysis Notes -->
                        @if(isset($competitor->ai_filter))
                            @php
                                $aiFilter = is_array($competitor->ai_filter) ? $competitor->ai_filter : json_decode($competitor->ai_filter, true);
                                $aiNotes = $aiFilter['ai_notes'] ?? null;
                                $qualityScore = $aiFilter['quality_score'] ?? null;
                                $relevanceScore = $aiFilter['relevance_score'] ?? null;
                            @endphp
                            
                            @if($aiNotes || $qualityScore || $relevanceScore)
                                <div class="ai-analysis mt-3 p-3 bg-light rounded border border-info">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-robot text-info me-2 fs-5"></i>
                                        <div class="flex-grow-1">
                                            <strong class="text-info">AI Analysis:</strong>
                                            @if($qualityScore || $relevanceScore)
                                                <div class="mt-2 d-flex gap-3">
                                                    @if($qualityScore)
                                                        <span class="badge bg-info">Quality: {{ $qualityScore }}%</span>
                                                    @endif
                                                    @if($relevanceScore)
                                                        <span class="badge bg-info">Relevance: {{ $relevanceScore }}%</span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if($aiNotes)
                                                <p class="mt-2 mb-0 small text-muted">{{ $aiNotes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if($competitor->feedbacks->isEmpty())
                        <!-- Verification Form -->
                        <div class="verification-section bg-light p-4 rounded">
                            <h5 class="mb-4">Is this information correct?</h5>

                            <form action="{{ route('feedback.submit') }}" method="POST" class="verification-form">
                                @csrf
                                <input type="hidden" name="competitor_id" value="{{ $competitor->id }}">
                                <input type="hidden" name="research_request_id" value="{{ $researchRequestId }}">
                                <input type="hidden" name="feedback_type" value="relevance">

                                <div class="row g-4">
                                    <!-- Is Relevant -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Is this a relevant competitor?</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="is_relevant" value="1" 
                                                   id="relevant-yes-{{ $competitor->id }}" required>
                                            <label class="btn btn-outline-success" for="relevant-yes-{{ $competitor->id }}">
                                                <i class="bi bi-check-circle me-1"></i> Yes
                                            </label>

                                            <input type="radio" class="btn-check" name="is_relevant" value="0" 
                                                   id="relevant-no-{{ $competitor->id }}">
                                            <label class="btn btn-outline-danger" for="relevant-no-{{ $competitor->id }}">
                                                <i class="bi bi-x-circle me-1"></i> No
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Is Useful -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Is this information useful?</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="is_useful" value="1" 
                                                   id="useful-yes-{{ $competitor->id }}" required>
                                            <label class="btn btn-outline-success" for="useful-yes-{{ $competitor->id }}">
                                                <i class="bi bi-check-circle me-1"></i> Yes
                                            </label>

                                            <input type="radio" class="btn-check" name="is_useful" value="0" 
                                                   id="useful-no-{{ $competitor->id }}">
                                            <label class="btn btn-outline-danger" for="useful-no-{{ $competitor->id }}">
                                                <i class="bi bi-x-circle me-1"></i> No
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Is Accurate -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Is the data accurate?</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="is_accurate" value="1" 
                                                   id="accurate-yes-{{ $competitor->id }}" required>
                                            <label class="btn btn-outline-success" for="accurate-yes-{{ $competitor->id }}">
                                                <i class="bi bi-check-circle me-1"></i> Yes
                                            </label>

                                            <input type="radio" class="btn-check" name="is_accurate" value="0" 
                                                   id="accurate-no-{{ $competitor->id }}">
                                            <label class="btn btn-outline-danger" for="accurate-no-{{ $competitor->id }}">
                                                <i class="bi bi-x-circle me-1"></i> No
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Overall Rating (1-5)</label>
                                        <div class="rating-stars d-flex gap-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <input type="radio" class="btn-check" name="overall_rating" value="{{ $i }}" 
                                                       id="rating-{{ $i }}-{{ $competitor->id }}" required>
                                                <label class="btn btn-outline-warning star-btn" for="rating-{{ $i }}-{{ $competitor->id }}">
                                                    <i class="bi bi-star-fill"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <!-- Spam/Duplicate Flags -->
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="is_spam" value="1" 
                                                   id="spam-{{ $competitor->id }}">
                                            <label class="form-check-label text-danger" for="spam-{{ $competitor->id }}">
                                                <i class="bi bi-exclamation-triangle me-1"></i> Report as Spam
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="is_duplicate" value="1" 
                                                   id="duplicate-{{ $competitor->id }}">
                                            <label class="form-check-label text-warning" for="duplicate-{{ $competitor->id }}">
                                                <i class="bi bi-files me-1"></i> Mark as Duplicate
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Comments -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Additional Comments (Optional)</label>
                                        <textarea class="form-control" name="comments" rows="3" 
                                                  placeholder="Any additional feedback or corrections..."></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Submit Verification
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                        <!-- Already Verified -->
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-3 fs-3"></i>
                            <div>
                                <strong>Verified!</strong> Thank you for your feedback on this competitor.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                    <h4>No Competitors Found</h4>
                    <p>There are no competitors to verify for this research request.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Completion Section -->
    @if($verifiedCount === $totalCount && $totalCount > 0)
    <div class="completion-section mt-5">
        <div class="alert alert-success text-center py-5">
            <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-3"></i>
            <h3 class="mb-3">All Competitors Verified!</h3>
            <p class="lead mb-4">Thank you for your feedback. This helps improve our results.</p>
            <a href="{{ route('market-research.report', $researchRequestId) }}" class="btn btn-success btn-lg">
                <i class="bi bi-file-text me-2"></i>
                View Complete Report
            </a>
        </div>
    </div>
    @endif
</div>

<style>
.competitor-card {
    transition: all 0.3s ease;
}

.competitor-card.border-success {
    opacity: 0.85;
}

.star-btn {
    font-size: 1.5rem;
}

.star-btn:hover,
.btn-check:checked + .star-btn {
    background-color: #ffc107;
    border-color: #ffc107;
    color: white;
}

.verification-form .btn-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.social-media-section a {
    transition: transform 0.2s ease;
}

.social-media-section a:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to next unverified competitor after submission
    const forms = document.querySelectorAll('.verification-form');
    forms.forEach((form, index) => {
        form.addEventListener('submit', function() {
            setTimeout(() => {
                const nextCard = document.querySelectorAll('.competitor-card:not(.border-success)')[0];
                if (nextCard) {
                    nextCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 500);
        });
    });
});
</script>
@endsection
