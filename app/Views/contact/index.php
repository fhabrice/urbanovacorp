<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1><?php echo __('contact.title'); ?></h1>
        <p><?php echo __('contact.description'); ?></p>
    </div>

    <div class="contact-container">
        <div class="contact-form">
            <form method="POST" action="/contact">
                <div class="form-group">
                    <label for="name"><?php echo __('contact.name'); ?> *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email"><?php echo __('contact.email'); ?> *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone"><?php echo __('contact.phone'); ?></label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="company"><?php echo __('contact.company'); ?></label>
                    <input type="text" id="company" name="company">
                </div>

                <div class="form-group">
                    <label for="type"><?php echo __('contact.type'); ?></label>
                    <select id="type" name="type">
                        <option value="general"><?php echo __('contact.type_general'); ?></option>
                        <option value="quote"><?php echo __('contact.type_quote'); ?></option>
                        <option value="partnership"><?php echo __('contact.type_partnership'); ?></option>
                        <option value="investor"><?php echo __('contact.type_investor'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject"><?php echo __('contact.subject'); ?></label>
                    <input type="text" id="subject" name="subject">
                </div>

                <div class="form-group">
                    <label for="message"><?php echo __('contact.message'); ?> *</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo __('contact.send'); ?></button>
            </form>
        </div>

        <div class="contact-info">
            <div class="info-card">
                <h3>URBANOVA SOLUTIONS</h3>
                <p>Kinshasa, République Démocratique du Congo</p>
            </div>

            <div class="info-card">
                <h3><?php echo __('footer.contact'); ?></h3>
                <ul>
                    <li><i class="fas fa-phone"></i> +243 XXX XXX XXX</li>
                    <li><i class="fas fa-envelope"></i> contact@urbanova.cd</li>
                </ul>
            </div>

            <div class="info-card">
                <h3><?php echo __('footer.follow'); ?></h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.contact-form {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.info-card ul {
    list-style: none;
}

.info-card li {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
