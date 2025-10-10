// Payment Modal Management
let currentPayment = {
    title: '',
    amount: 0,
    type: '',
    semester: null
};

// Open Payment Modal
function openPaymentModal(title, amount, type, semester) {
    currentPayment = { title, amount, type, semester };
    
    // Update modal content
    document.getElementById('modalPaymentTitle').textContent = title;
    document.getElementById('modalAmount').textContent = `₹${amount.toLocaleString()}`;
    
    // Generate QR Code (in real implementation, this would be dynamic)
    generateQRCode(title, amount);
    
    // Show modal
    const modal = document.getElementById('paymentModal');
    modal.classList.add('active');
    
    // Reset to QR payment method
    selectPaymentMethod('qr');
}

// Close Payment Modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}

// Select Payment Method
function selectPaymentMethod(method) {
    // Remove active class from all buttons
    const methodBtns = document.querySelectorAll('.method-btn');
    methodBtns.forEach(btn => btn.classList.remove('active'));
    
    // Hide all payment sections
    const paymentSections = document.querySelectorAll('.payment-section');
    paymentSections.forEach(section => section.classList.remove('active'));
    
    // Activate selected method
    if (method === 'qr') {
        document.querySelector('.method-btn:nth-child(1)').classList.add('active');
        document.getElementById('qrPaymentSection').classList.add('active');
    } else if (method === 'card') {
        document.querySelector('.method-btn:nth-child(2)').classList.add('active');
        document.getElementById('cardPaymentSection').classList.add('active');
    } else if (method === 'upi') {
        document.querySelector('.method-btn:nth-child(3)').classList.add('active');
        document.getElementById('upiPaymentSection').classList.add('active');
    }
}

// Generate QR Code (Simplified - in real app, use QR library)
function generateQRCode(title, amount) {
    // In a real implementation, you would use a library like qrcode.js
    // to generate an actual QR code with UPI payment URL
    // For now, we're showing a placeholder icon
    
    // Example UPI payment URL format:
    // upi://pay?pa=university@upi&pn=University&am=17000&cu=INR&tn=Semester5Fee
    
    const qrCode = document.getElementById('qrCode');
    qrCode.innerHTML = '<i class="fas fa-qrcode"></i>';
    
    // In production, replace with:
    // const upiUrl = `upi://pay?pa=university@upi&pn=University&am=${amount}&cu=INR&tn=${title.replace(/\s/g, '')}`;
    // new QRCode(qrCode, upiUrl);
}

// Download Receipt
function downloadReceipt(transactionId) {
    // Simulate receipt download
    showNotification(`Downloading receipt for transaction ${transactionId}...`, 'success');
    
    // In real implementation, this would generate and download a PDF receipt
    setTimeout(() => {
        showNotification('Receipt downloaded successfully!', 'success');
    }, 1000);
}

// Process Payment (Simulated)
function processPayment(method) {
    showNotification('Processing payment...', 'info');
    
    // Simulate payment processing
    setTimeout(() => {
        const success = Math.random() > 0.1; // 90% success rate for demo
        
        if (success) {
            const transactionId = 'TXN' + Date.now();
            showNotification(`Payment successful! Transaction ID: ${transactionId}`, 'success');
            
            // Close modal after short delay
            setTimeout(() => {
                closePaymentModal();
                // Refresh payment status (in real app, reload from server)
                updatePaymentStatus(currentPayment);
            }, 2000);
        } else {
            showNotification('Payment failed. Please try again.', 'error');
        }
    }, 2000);
}

// Update Payment Status after successful payment
function updatePaymentStatus(payment) {
    // Find and update the pending card
    const cards = document.querySelectorAll('.payment-card.pending-card');
    
    cards.forEach(card => {
        const cardType = card.dataset.feeType;
        const cardSemester = parseInt(card.dataset.semester);
        
        if (cardType === payment.type && 
            (payment.semester === null || cardSemester === payment.semester)) {
            
            // Update status badge
            const statusBadge = card.querySelector('.status-badge');
            statusBadge.className = 'status-badge paid';
            statusBadge.innerHTML = '<i class="fas fa-check"></i> Paid';
            
            // Remove pending styling
            card.classList.remove('pending-card');
            
            // Disable pay button
            const payBtn = card.querySelector('.btn-pay');
            payBtn.textContent = 'Paid';
            payBtn.style.background = 'rgba(76, 175, 80, 0.5)';
            payBtn.disabled = true;
            payBtn.style.cursor = 'not-allowed';
            
            // Add to payment history
            addToPaymentHistory(payment);
        }
    });
    
    // Update semester overview if applicable
    if (payment.semester) {
        const overviewCard = document.querySelector(`.overview-card[data-semester="${payment.semester}"]`);
        if (overviewCard) {
            const statusBadge = overviewCard.querySelector('.status-badge');
            statusBadge.className = 'status-badge paid';
            statusBadge.innerHTML = '<i class="fas fa-check"></i> Paid';
        }
    }
    
    // Update summary stats
    updateSummaryStats();
}

// Add to Payment History
function addToPaymentHistory(payment) {
    const historyCard = document.querySelector('.history-card');
    const transactionId = 'TXN' + Date.now();
    const currentDate = new Date().toLocaleDateString('en-IN', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
    
    const historyItem = document.createElement('div');
    historyItem.className = 'history-item';
    historyItem.innerHTML = `
        <div class="history-icon paid">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="history-details">
            <h4>${payment.title}</h4>
            <p>Transaction ID: ${transactionId}</p>
            <span class="history-date">${currentDate}</span>
        </div>
        <div class="history-amount">
            <span class="amount">₹${payment.amount.toLocaleString()}</span>
            <button class="btn-receipt" onclick="downloadReceipt('${transactionId}')">
                <i class="fas fa-download"></i> Receipt
            </button>
        </div>
    `;
    
    // Add at the beginning of history
    historyCard.insertBefore(historyItem, historyCard.firstChild);
}

// Update Summary Stats
function updateSummaryStats() {
    // Calculate total paid and pending
    let totalPaid = 0;
    let totalPending = 0;
    
    // Check all payment cards
    const allCards = document.querySelectorAll('.payment-card');
    allCards.forEach(card => {
        const amount = parseInt(card.querySelector('.amount').textContent.replace(/[₹,]/g, ''));
        const statusBadge = card.querySelector('.status-badge');
        
        if (statusBadge.classList.contains('paid')) {
            totalPaid += amount;
        } else if (statusBadge.classList.contains('pending')) {
            totalPending += amount;
        }
    });
    
    // Also add history amounts
    const historyAmounts = document.querySelectorAll('.history-amount .amount');
    historyAmounts.forEach(amountEl => {
        const amount = parseInt(amountEl.textContent.replace(/[₹,]/g, ''));
        totalPaid += amount;
    });
    
    // Update display
    const paidDisplay = document.querySelector('.stat-item:not(.pending) .stat-value');
    const pendingDisplay = document.querySelector('.stat-item.pending .stat-value');
    
    if (paidDisplay) paidDisplay.textContent = `₹${totalPaid.toLocaleString()}`;
    if (pendingDisplay) pendingDisplay.textContent = `₹${totalPending.toLocaleString()}`;
}

// Show Notification (Toast)
function showNotification(message, type = 'info') {
    // Remove existing notification if any
    const existingNotif = document.querySelector('.notification-toast');
    if (existingNotif) {
        existingNotif.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles if not already present
    if (!document.getElementById('notificationStyles')) {
        const style = document.createElement('style');
        style.id = 'notificationStyles';
        style.textContent = `
            .notification-toast {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: rgba(43, 46, 74, 0.95);
                backdrop-filter: blur(20px);
                border: 2px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                padding: 15px 25px;
                display: flex;
                align-items: center;
                gap: 15px;
                color: white;
                font-size: 1rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                animation: slideInRight 0.3s ease;
                z-index: 10000;
            }
            .notification-toast.success {
                border-color: #4caf50;
            }
            .notification-toast.error {
                border-color: #f44336;
            }
            .notification-toast.info {
                border-color: #2196f3;
            }
            .notification-toast i {
                font-size: 1.5rem;
            }
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add event listeners to submit buttons
document.addEventListener('DOMContentLoaded', function() {
    // Card payment submit
    const cardSubmitBtns = document.querySelectorAll('#cardPaymentSection .btn-submit-payment');
    cardSubmitBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            processPayment('card');
        });
    });
    
    // UPI payment submit
    const upiSubmitBtns = document.querySelectorAll('#upiPaymentSection .btn-submit-payment');
    upiSubmitBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            processPayment('upi');
        });
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentModal();
        }
    });
    
    // Format card number input
    const cardNumberInput = document.querySelector('#cardPaymentSection input[type="text"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }
    
    // Format expiry date input
    const expiryInput = document.querySelector('#cardPaymentSection input[placeholder="MM/YY"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    }
    
    // Initialize summary stats
    updateSummaryStats();
});

// Simulate QR code payment verification
function simulateQRPayment() {
    // This would be called by a webhook/polling mechanism in real app
    // when payment is detected through QR code
    setTimeout(() => {
        processPayment('qr');
    }, 5000); // Simulate 5 second delay
}

// Search functionality (if search bar is added)
function searchPayments(query) {
    const cards = document.querySelectorAll('.payment-card, .overview-card');
    query = query.toLowerCase();
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Export functions for external use
window.paymentPortal = {
    openPaymentModal,
    closePaymentModal,
    selectPaymentMethod,
    downloadReceipt,
    processPayment,
    searchPayments
};
