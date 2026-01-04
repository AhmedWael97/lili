# Data Verification Flow

## Overview
The market research system now requires user verification before showing final reports. This ensures data quality and enables continuous learning.

## New Workflow

### 1. Research Processing
```
User submits research → Job processes → Competitors found → Status: "pending_verification"
```

### 2. User Verification
```
User sees "Verify Data" button → Reviews each competitor → Marks as useful/correct/spam/duplicate
```

### 3. Report Generation
```
All competitors verified → Status: "completed" → Report becomes available
```

## User Interface Changes

### Research Requests List
- **New Status**: "pending_verification" (Orange badge with "Verify Data" button)
- **Action Button**: Pulsing orange button that redirects to verification page
- **Status Badge**: Orange background indicating verification needed

### Verification Page (`/market-research/{id}/verify`)
- Progress bar showing X / Y verified
- Each competitor card with details (website, phone, address, social media)
- Feedback form with:
  - Relevance (useful/not useful)
  - Accuracy (correct/incorrect)
  - Quality rating (1-5 stars)
  - Spam flag
  - Duplicate flag
  - Notes field

### Report Page
- **Before Verification**: Redirects to verification page with info message
- **After Verification**: Shows full report with all insights

## Backend Changes

### ResearchRequest Model
**New Methods:**
- `markAsPendingVerification()` - Sets status to "pending_verification"
- `needsVerification()` - Returns true if status is "pending_verification"
- `isVerified()` - Returns true if status is "completed"

**Status Values:**
- `pending` - Waiting to start
- `processing` - Currently running
- `pending_verification` - Ready for user verification ⭐ NEW
- `completed` - Verified and report ready
- `failed` - Processing failed

### ProcessMarketResearch Job
**Changed:**
- After generating report, marks as `pending_verification` instead of `completed`
- Log message: "Market research completed - pending user verification"

### FeedbackWebController
**Enhanced `submitFeedback()`:**
- After each feedback submission, checks if all competitors verified
- If all verified AND status is "pending_verification", marks as "completed"
- Redirects to report page when fully verified
- Otherwise returns to verification page

### MarketResearchWebController
**Enhanced `show()`:**
- Checks if research needs verification
- Redirects to verification page if not verified
- Only shows report when status is "completed"

## API Endpoints

### Verification Flow
```
GET  /market-research/{id}/verify         - Show verification page
POST /feedback/submit                      - Submit single feedback
```

### Report Access
```
GET  /market-research/report/{id}         - View report (requires verification)
```

## How It Works

1. **Job Completes**: 
   - Finds 10 competitors
   - Analyzes social media
   - Generates insights
   - Status → `pending_verification`

2. **User Notification**:
   - "Research complete - please verify data"
   - Orange "Verify Data" button appears

3. **Verification Process**:
   - User reviews each competitor
   - Marks useful/not useful
   - Flags spam or duplicates
   - Rates quality (1-5 stars)
   - Progress bar updates: "3 / 10 verified"

4. **Auto-Completion**:
   - When all 10 verified
   - System marks research as `completed`
   - Redirects to report automatically
   - Success message: "All data verified! Your report is ready."

5. **Report Access**:
   - Now user can view full report
   - "View Report" button turns green
   - All insights and recommendations available

## Learning System

### Feedback Collection
Every verification feeds into the learning system:
- Trust scores calculated per competitor
- Patterns identified (good vs bad results)
- Thresholds adjusted automatically

### Continuous Improvement
- Next research uses updated thresholds
- Better competitor filtering
- Improved relevance scoring
- Spam detection enhancement

## Testing

### Test the Flow:
1. Start a new market research
2. Wait for job to complete
3. Check status is "pending_verification"
4. Click "Verify Data" button
5. Verify each competitor
6. After last verification, should redirect to report
7. Report should load successfully

### Restart Queue Worker:
```bash
php artisan queue:restart
php artisan queue:work redis --tries=3
```

## Benefits

✅ **Data Quality**: Users verify accuracy before seeing reports
✅ **User Trust**: Users feel in control of the data
✅ **Continuous Learning**: System improves with each verification
✅ **Spam Prevention**: Users can flag bad results
✅ **Duplicate Detection**: Users identify duplicates
✅ **Better Results**: Future searches benefit from feedback
