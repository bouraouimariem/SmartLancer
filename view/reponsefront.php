<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/reponse.php';
require_once __DIR__ . '/../model/avis.php';

$database = new Database();
$db = $database->getConnection();
$reponseModel = new Reponse($db);
$avisModel = new Avis($db);

$avisId = isset($_GET['avis_id']) ? (int)$_GET['avis_id'] : 0;
$avis = null;
$reponses = [];
if ($avisId > 0) {
    $avis = $avisModel->getAvisById($avisId);
    if ($avis) $reponses = $reponseModel->getByAvisId($avisId);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Réponses</title>
<style>
/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #d0e8ff, #f0f9ff);
    color: #333;
    padding: 30px 20px;
    min-height: 100vh;
    line-height: 1.6;
}

.container {
    max-width: 900px;
    margin: 0 auto;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Card Component */
.card {
    background: #fff;
    padding: 28px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    border-top: 4px solid #1E90FF;
    transition: all 0.3s ease;
    animation: slideInDown 0.4s ease-out;
    position: relative;
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.card:hover::before {
    left: 100%;
}

.card:hover {
    box-shadow: 0 12px 30px rgba(30,144,255,0.2);
    transform: translateY(-4px);
    border-top-color: #0a74d6;
}

.card h2 {
    color: #1E90FF;
    margin-bottom: 16px;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.card h3 {
    color: #1E90FF;
    margin-bottom: 12px;
    font-size: 16px;
    font-weight: 600;
}

.card p {
    color: #555;
    line-height: 1.7;
    margin-bottom: 12px;
}

/* Button Base */
.btn {
    background: #1E90FF;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30,144,255,0.35);
}

.btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 10px rgba(30,144,255,0.25);
}

.btn.secondary {
    background: #ccc;
    color: #333;
}

.btn.secondary:hover {
    background: #aaa;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Response Item */
.reponse-item {
    border-left: 4px solid #1E90FF;
    padding: 18px;
    border-radius: 8px;
    background: #f9f9f9;
    margin-bottom: 14px;
    transition: all 0.3s ease;
    position: relative;
}

.reponse-item::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #1E90FF, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.reponse-item:hover {
    background: #fff;
    box-shadow: 0 4px 15px rgba(30,144,255,0.2);
    border-left-color: #0a74d6;
    transform: translateX(4px);
}

.reponse-item:hover::after {
    opacity: 1;
}

.reponse-item strong {
    color: #1E90FF;
    font-size: 15px;
    display: block;
}

.reponse-item small {
    color: #999;
    font-size: 12px;
    display: block;
    margin-bottom: 10px;
}

/* Form Group */
.form-group {
    margin-bottom: 18px;
    display: flex;
    flex-direction: column;
    animation: slideInDown 0.4s ease-out backwards;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.15s; }
.form-group:nth-child(3) { animation-delay: 0.2s; }
.form-group:nth-child(4) { animation-delay: 0.25s; }

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
    font-size: 13px;
    text-transform: capitalize;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="file"],
.form-group textarea,
.form-group select {
    padding: 12px 14px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #fff;
}

.form-group input[type="text"]::placeholder,
.form-group input[type="email"]::placeholder,
.form-group textarea::placeholder {
    color: #bbb;
    font-weight: 400;
}

.form-group input[type="text"]:hover,
.form-group input[type="email"]:hover,
.form-group input[type="file"]:hover,
.form-group textarea:hover,
.form-group select:hover {
    border-color: #1E90FF;
    background: #f9f9f9;
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group input[type="file"]:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #1E90FF;
    box-shadow: 0 0 8px rgba(30,144,255,0.4);
    background: #fff;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
    line-height: 1.5;
}

.form-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin-right: 8px;
    accent-color: #1E90FF;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.form-group input[type="checkbox"]:hover {
    transform: scale(1.15);
}

.form-group input[type="checkbox"]:checked {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #1E90FF;
}

.form-group input[type="file"] {
    padding: 10px;
}

.radio-group,
.checkbox-group {
    display: flex;
    gap: 18px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.radio-group label,
.checkbox-group label {
    display: flex;
    align-items: center;
    font-weight: 400;
    margin-bottom: 0;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.radio-group label:hover,
.checkbox-group label:hover {
    color: #1E90FF;
    transform: translateX(3px);
}

.form-row {
    display: flex;
    gap: 16px;
}

.form-row .form-group {
    flex: 1;
}

.btn-group {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #e0f0ff;
}

.btn-primary {
    background: linear-gradient(135deg, #1E90FF 0%, #0a74d6 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(30,144,255,0.25);
}

.btn-primary:hover {
    box-shadow: 0 8px 24px rgba(30,144,255,0.4);
    transform: translateY(-3px);
}

.btn-secondary {
    background: #ddd;
    color: #333;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-secondary:hover {
    background: #ccc;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Visibility Badge */
.visibilite-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 8px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    animation: slideInDown 0.3s ease-out;
}

.visibilite-public {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.visibilite-private {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Attachment Preview */
.attachment-preview {
    margin-top: 12px;
    padding: 12px 16px;
    background: #f9f9f9;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #555;
    animation: slideInDown 0.3s ease-out;
}

.attachment-preview a {
    color: #1E90FF;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.attachment-preview a:hover {
    color: #32CD32;
    text-decoration: underline;
}

.optional-label {
    font-size: 11px;
    color: #999;
    font-weight: 400;
    opacity: 0.85;
    text-transform: lowercase;
}

.required-badge {
    color: #FF4C4C;
    font-weight: 700;
    margin-left: 2px;
}

.help-text {
    font-size: 12px;
    color: #666;
    margin-top: 6px;
    font-style: italic;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f0f8ff;
}

::-webkit-scrollbar-thumb {
    background: #1E90FF;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #0a74d6;
}

/* Loading */
.loading {
    pointer-events: none;
    opacity: 0.6;
}

/* Responsive */
@media (max-width: 768px) {
    body {
        padding: 20px 12px;
    }

    .card {
        padding: 20px;
    }

    .card h2 {
        font-size: 19px;
    }

    .radio-group,
    .checkbox-group {
        flex-direction: column;
        gap: 14px;
    }

    .form-row {
        flex-direction: column;
        gap: 0;
    }

    .btn-group {
        flex-direction: column;
        gap: 10px;
    }

    .btn {
        width: 100%;
        justify-content: center;
        padding: 13px 16px;
    }

    .reponse-item {
        padding: 14px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 16px 10px;
    }

    .card {
        padding: 14px;
        margin-bottom: 16px;
    }

    .card h2 {
        font-size: 18px;
        margin-bottom: 12px;
    }

    .btn-group {
        gap: 8px;
    }

    .form-group label {
        font-size: 12px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        font-size: 16px;
        padding: 11px 12px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .btn {
        font-size: 13px;
        padding: 11px 14px;
    }

    .container {
        padding: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

.card {
    background: #fff;
    padding: 28px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    margin-bottom: 20px;
    border-top: 4px solid var(--primary);
    transition: var(--transition);
    animation: slideInDown 0.4s ease-out;
    position: relative;
    overflow: hidden;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.card:hover::before {
    left: 100%;
}

.card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
    border-top-color: var(--success);
}

.card h2 {
    color: var(--primary);
    margin-bottom: 16px;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.card h3 {
    color: var(--primary);
    margin-bottom: 12px;
    font-size: 16px;
    font-weight: 600;
}

.card p {
    color: var(--gray-600);
    line-height: 1.7;
    margin-bottom: 12px;
}

.btn {
    background: var(--primary);
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(7, 94, 58, 0.35);
}

.btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 10px rgba(7, 94, 58, 0.25);
}

.btn.secondary {
    background: var(--gray-300);
    color: var(--gray-dark);
}

.btn.secondary:hover {
    background: #c0c0c0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.reponse-item {
    border-left: 4px solid var(--primary);
    padding: 18px;
    border-radius: 8px;
    background: var(--gray-50);
    margin-bottom: 14px;
    transition: var(--transition);
    position: relative;
}

.reponse-item::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), transparent);
    opacity: 0;
    transition: opacity var(--transition);
}

.reponse-item:hover {
    background: #fff;
    box-shadow: var(--shadow-md);
    border-left-color: var(--success);
    transform: translateX(4px);
}

.reponse-item:hover::after {
    opacity: 1;
}

.reponse-item strong {
    color: var(--primary);
    font-size: 15px;
    display: block;
    margin-bottom: 4px;
}

.reponse-item small {
    color: #999;
    font-size: 12px;
    display: block;
    margin-bottom: 10px;
}

.form-group {
    margin-bottom: 18px;
    display: flex;
    flex-direction: column;
    animation: slideInDown 0.4s ease-out backwards;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.15s; }
.form-group:nth-child(3) { animation-delay: 0.2s; }
.form-group:nth-child(4) { animation-delay: 0.25s; }

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--gray-dark);
    font-size: 13px;
    text-transform: capitalize;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="file"],
.form-group textarea,
.form-group select {
    padding: 12px 14px;
    border: 1.5px solid var(--gray-300);
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    transition: var(--transition);
    background: #fff;
}

.form-group input[type="text"]::placeholder,
.form-group input[type="email"]::placeholder,
.form-group textarea::placeholder {
    color: #bbb;
    font-weight: 400;
}

.form-group input[type="text"]:hover,
.form-group input[type="email"]:hover,
.form-group input[type="file"]:hover,
.form-group textarea:hover,
.form-group select:hover {
    border-color: var(--primary);
    background: var(--gray-50);
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group input[type="file"]:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(7, 94, 58, 0.15);
    background: #fff;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
    line-height: 1.5;
}

.form-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin-right: 8px;
    accent-color: var(--primary);
    transition: var(--transition);
    flex-shrink: 0;
}

.form-group input[type="checkbox"]:hover {
    transform: scale(1.15);
}

.form-group input[type="checkbox"]:checked {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--primary);
}

.form-group input[type="file"] {
    padding: 10px;
}

.radio-group,
.checkbox-group {
    display: flex;
    gap: 18px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.radio-group label,
.checkbox-group label {
    display: flex;
    align-items: center;
    font-weight: 400;
    margin-bottom: 0;
    cursor: pointer;
    font-size: 14px;
    transition: var(--transition);
}

.radio-group label:hover,
.checkbox-group label:hover {
    color: var(--primary);
    transform: translateX(3px);
}

.form-row {
    display: flex;
    gap: 16px;
}

.form-row .form-group {
    flex: 1;
}

.btn-group {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid var(--gray-200);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(7, 94, 58, 0.25);
}

.btn-primary:hover {
    box-shadow: 0 8px 24px rgba(7, 94, 58, 0.4);
    transform: translateY(-3px);
}

.btn-secondary {
    background: var(--gray-300);
    color: var(--gray-dark);
    box-shadow: var(--shadow-sm);
}

.btn-secondary:hover {
    background: #c5c5c5;
    box-shadow: var(--shadow-md);
}

.visibilite-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 8px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    animation: slideInDown 0.3s ease-out;
}

.visibilite-public {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #b1dfbb;
}

.visibilite-private {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f1b0b7;
}

.attachment-preview {
    margin-top: 12px;
    padding: 12px 16px;
    background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-50) 100%);
    border-left: 4px solid var(--warning);
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #555;
    animation: slideInDown 0.3s ease-out;
}

.attachment-preview a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.attachment-preview a:hover {
    color: var(--success);
    text-decoration: underline;
}

.optional-label {
    font-size: 11px;
    color: #999;
    font-weight: 400;
    opacity: 0.85;
    text-transform: lowercase;
}

.required-badge {
    color: var(--danger);
    font-weight: 700;
    margin-left: 2px;
}

.help-text {
    font-size: 12px;
    color: var(--gray-600);
    margin-top: 6px;
    font-style: italic;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
}

::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

/* Loading animation */
.loading {
    pointer-events: none;
    opacity: 0.6;
}

/* Media Queries */
@media (max-width: 768px) {
    body {
        padding: 20px 12px;
    }

    .card {
        padding: 20px;
    }

    .card h2 {
        font-size: 19px;
    }

    .radio-group,
    .checkbox-group {
        flex-direction: column;
        gap: 14px;
    }

    .form-row {
        flex-direction: column;
        gap: 0;
    }

    .btn-group {
        flex-direction: column;
        gap: 10px;
    }

    .btn {
        width: 100%;
        justify-content: center;
        padding: 13px 16px;
    }

    .reponse-item {
        padding: 14px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 16px 10px;
    }

    .card {
        padding: 14px;
        margin-bottom: 16px;
    }

    .card h2 {
        font-size: 18px;
        margin-bottom: 12px;
    }

    .btn-group {
        gap: 8px;
    }

    .form-group label {
        font-size: 12px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        font-size: 16px;
        padding: 11px 12px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .btn {
        font-size: 13px;
        padding: 11px 14px;
    }

    .container {
        padding: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

@media (prefers-color-scheme: dark) {
    :root {
        --gray-dark: #e0e0e0;
        --gray-50: #1e1e1e;
    }

    body {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #e0e0e0;
    }

    .card {
        background: #2d2d2d;
        color: #e0e0e0;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        background: #1e1e1e;
        color: #e0e0e0;
        border-color: #444;
    }

    .form-group input::placeholder {
        color: #888;
    }
}
</style>
</head>
<body>
<div class="container">
<?php if (!$avisId || !$avis): ?>
    <div class="card">
        <h2>Avis introuvable</h2>
        <p>Fournissez un paramètre <code>?avis_id=ID</code> pour voir les réponses.</p>
    </div>
<?php else: ?>
    <div class="card">
        <h2>Avis de <?= htmlspecialchars($avis['nom']) ?></h2>
        <p><?= nl2br(htmlspecialchars($avis['contenu'])) ?></p>
        <p>Note: <?= (int)$avis['note'] ?>/5</p>
        <button class="btn open-reponse-btn" data-avis-id="<?= $avisId ?>">Ajouter une réponse</button>
    </div>

    <div class="card">
        <h3>Réponses (<?= count($reponses) ?>)</h3>
        <div id="responses-<?= $avisId ?>">
            <?php if (count($reponses) === 0): ?>
                <div class="reponse-item">Aucune réponse pour le moment.</div>
            <?php else: ?>
                <?php foreach ($reponses as $r): ?>
                    <div class="reponse-item"
                         data-id="<?= $r['id'] ?>"
                         data-avis-id="<?= $r['avis_id'] ?>"
                         data-nom="<?= htmlspecialchars($r['nom'], ENT_QUOTES) ?>"
                         data-email="<?= htmlspecialchars($r['email'], ENT_QUOTES) ?>"
                         data-contenu="<?= htmlspecialchars($r['contenu'], ENT_QUOTES) ?>"
                         data-type="<?= isset($r['type']) ? htmlspecialchars($r['type'], ENT_QUOTES) : 'freelance' ?>"
                         data-piece="<?= isset($r['piece_jointe']) ? htmlspecialchars($r['piece_jointe'], ENT_QUOTES) : '' ?>"
                         >
                        <strong><?= htmlspecialchars($r['nom']) ?></strong>
                        <small style="color:#666;margin-left:8px"><?= isset($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '' ?></small>
                        <p><?= nl2br(htmlspecialchars($r['contenu'])) ?></p>

                        <!-- is_online and statut are shown only in backoffice -->

                        <?php if (isset($r['type']) && $r['type']): ?>
                            <div style="margin-top:6px"><strong>Type:</strong> <span style="background:#cce5ff;padding:4px 8px;border-radius:4px;color:#004085"><?= htmlspecialchars($r['type']) ?></span></div>
                        <?php endif; ?>

                        <?php if (isset($r['piece_jointe']) && $r['piece_jointe']): ?>
                            <div style="margin-top:8px"><strong>Pièce jointe:</strong> <a href="/validationmodule/<?= htmlspecialchars($r['piece_jointe']) ?>" target="_blank" style="color:#075e3a;text-decoration:underline;">Ouvrir</a></div>
                        <?php endif; ?>

                        <div style="display:flex;gap:8px;margin-top:8px">
                            <button class="btn secondary edit-reponse" data-id="<?= $r['id'] ?>">Modifier</button>
                            <button class="btn secondary delete-reponse" data-id="<?= $r['id'] ?>">Supprimer</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</div>
<?php
    $scriptPath = __DIR__ . '/reponse.js';
    $ver = file_exists($scriptPath) ? filemtime($scriptPath) : time();
?>
<script src="reponse.js?v=<?php echo $ver; ?>"></script>
</body>
</html>
