<style>
.cartpopup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparant achtergrond */
    z-index: 1000;
  }
  
  .popup-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    text-wrap: nowrap;
  }
  
  .popup-content .go {
    padding: 10px 20px;
    background-color: #ff6f61;
    color: white;
    border: none;
    text-decoration: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .popup-content .continue {
    padding: 0px 5px;
    /* color: white; */
    border: none;
    border-bottom: #e55447 2px solid;
    background: none;
    cursor: pointer;
  }

  .continue {
    margin-top: 6px;
    margin-bottom: 6px;
  }

  .buttons {
    display: flex;
    flex-direction: row;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
  }
  
  .popup-content .go:hover {
    background-color: #e55447;
  }

  .popup-content .continue:hover {
    color: #e55447;
  }

  .title h5 {
    color: #5cac3c;
    font-weight: normal;
    font-size: 1em;
    text-wrap: nowrap;
  }

  .title img {
    width: 20px;
    height: 20px;
  }

  .title {
    display: flex;
    flex-direction: row;
    gap: 10px;
    justify-content: center;
    align-items: center;
    padding: 10px;
  }
</style>

<div id="cart-popup" class="cartpopup">
        <div class="popup-content">
            <div class="title">
                <img src="./images/yes.png" alt="">
                <h5>Product has been added to your cart!</h5>
            </div>
            <div class="buttons">
                <a href="cart.php" class="go">To my shoppingcart</a>
                <a class="continue" onclick="closePopup()">continue shopping</a>
            </div>
        </div>
    </div>

    <script>
      function closePopup(){
        document.querySelector('.cartpopup').style.display = 'none';
      }
    </script>