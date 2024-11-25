<style>
    /* _____________________Footer_____________________ */

footer {
    background-color: #333;
    color: white;
    padding: 20px;
}

.footer-section {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    /* margin: 0 15px; */
    padding: 12px;
    gap: 20px;
    justify-items: center;
}

.footer-section h3 {
    color: #ff5722;
    margin-bottom: 5px;
}

.footer-section ul {
    list-style-type: none;
    padding: 0;
}

.hamburger  {
    display: none;
}

.footer-section ul li a {
    color: #ddd;
    text-decoration: none;
}

.footer-section ul li a:hover {
    color: white;
}

.kleineletters {
    font-size: 0.8em;
    margin-top: 15px;
    margin-left: 60px;
}

.footer-bottom {
    padding: 10px 0;
    font-size: small;
    text-align: center;
}

.paymentmethods img {
    width: 50px;
}

.paymentmethods {
    display: grid;
    grid-template-columns: repeat(8, 60px);
    gap: 0px;
    margin: 10px 10px;
    margin-left: 60px;
}
.kleineletters {
    line-height: 1.5em;
}
.kleineletters a {
    color: #717171;
    text-decoration: none;
}

.kleineletters a:hover {
    text-decoration: underline;
    color: red;
    cursor: pointer;
}

.footer-bottom {
    font-size: 0.7em;
}
@media (max-width: 600px) {
    .footer-section {
        grid-template-rows: repeat(5, 1fr);
        grid-template-columns: 1fr;
        text-align: center;
    }
    .paymentmethods {
        grid-template-columns: repeat(4, 50px);
        gap: 20px;
        justify-content: center;
        margin-top: 20px;
        margin-left: 0;
    }
    .paymentmethods img {
        width: 55px;
    }

    .kleineletters {
        text-align: center;
        margin-left: 0;
    }
}


</style>
<footer>
        <div class="footer-section">
            <div class="foot">
                <h3>Klantendienst</h3>
                <ul>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Veelgestelde vragen</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Over Ons</h3>
                <ul>
                    <li><a href="#">Ons Verhaal</a></li>
                    <li><a href="#">Ons Team</a></li>
                    <li><a href="#">Werken bij ons</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Gift Cards</a></li>
                    <li><a href="#">Verzending</a></li>
                    <li><a href="#">Retourneren</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>B2B</h3>
                <ul>
                    <li><a href="#">Bibliotheken</a></li>
                    <li><a href="#">Facturatie</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Volg Ons</h3>
                <ul>
                    <li><a href="instagram.com">Instagram</a></li>
                    <li><a href="Facebook.com">Facebook</a></li>
                    <li><a href="Twitter.com">X</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <div class="kleineletters"> 
        <p>
            <a href="#">VERKOOPVOORWAARDEN</a>
            &nbsp;|&nbsp;
            <a href="#">PRIVACYVERKLARING</a>
            &nbsp;|&nbsp;
            <a href="#">DISCLAIMER</a>
            &nbsp;|&nbsp;
            <a href="#">COOKIEVERKLARING</a>
            &nbsp;|&nbsp;
            <a href="#">VOORWAARDEN VOOR REVIEWS</a>
        </p>
    </div>

   
    <div class="paymentmethods">
        <img src="./images/bancontact.png" alt="bancontact">
        <img src="./images/visa.png" alt="visa">
        <img src="./images/mastercard.png" alt="mastercard">
        <img src="./images/applepay.png" alt="applepay">
        <img src="./images/kbc.png" alt="kbc">
        <img src="./images/belfius.png" alt="belfius">
        <img src="./images/ideal.png" alt="ideal">
        <img src="./images/overschrijving.png" alt="overschrijving">
    </div>


    <div class="footer-bottom">
    <p>© 2024 Pageturners</p>
