const simplerCheckout = (host, appId, buttonId, single) => {
  const checkout = single ? (new SimplerCheckoutSingle(host, appId, buttonId)) : (new SimplerCheckout(host, appId, buttonId));
  return (e) => checkout.checkout(e);
}

const SIMPLERWC_POPUP_WIDTH = 520;
const SIMPLERWC_POPUP_HEIGHT = 720;

class SimplerEmptyCartError extends Error {
  constructor() {
    super();
    this.message = 'Your cart is empty!';
  }
}

class SimplerNoVariationSelectedError extends Error {
  constructor() {
    super();
    this.message = 'Please select all required product options before proceeding';
  }
}

class SimplerProductOutOfStock extends Error {
  constructor() {
    super();
    this.message = 'One of the products in your cart is out of stock.';
  }
}

class SimplerInvalidProductQuantity extends Error {
  constructor() {
    super();
    this.message = 'Invalid quantity selection';
  }
}

class SimplerCheckout {
  constructor(host, appId, buttonId) {
    this.host = host;
    this.appId = appId;
    this.buttonId = buttonId;
    this.variationSelected = true;
  }

  toJSON() {
    const decoded = atob(document.getElementById(this.buttonId).value);
    return JSON.parse(decoded);
  }

  checkout(e) {
    e.preventDefault();
    const order = this.buildOrder();

    try {
      this.validateOrder(order);
    } catch (e) {
      window.alert(e.message);
      return false;
    }

    const url = this.buildUrl(order);
    const popup = openPopup(url);

    const overlay = buildOverlay(popup);
    document.body.classList.add("overlay-active");
    document.body.appendChild(overlay);

    window.addEventListener('message', handleOrderCreatedMessage);

    const timer = setInterval(() => {
      if (popup.closed) {
        removeOverlay(overlay);
        clearInterval(timer);
        window.removeEventListener('message', handleOrderCreatedMessage);
      }
    }, 250);

    return false;
  }

  buildUrl(order) {
    const items = order.items.map(item => {
      return {
        id: item.id,
        qty: item.quantity
      }
    })

    return `${this.host}?app=${this.appId}&prd=${encodeURIComponent(JSON.stringify(items))}&cur=${order.currency}&lang=${order.locale}`;
  }

  buildOrder() {
    const { cart, currency, locale } = this.toJSON();
    return {
      ...cart,
      items: cart.map(el => this.buildOrderItem(el)),
      currency,
      locale
    };
  }

  validateOrder(order) {
    if (order.items.length === 0) {
      throw new SimplerEmptyCartError();
    }

    if (!this.variationSelected) {
      throw new SimplerNoVariationSelectedError();
    }

    order.items.forEach((item) => {
      if (!item.in_stock && !item.backorders_allowed) {
        throw new SimplerProductOutOfStock();
      }

      if (item.quantity <= 0) {
        throw new SimplerInvalidProductQuantity();
      }

      if (item.stock_quantity > 0 && item.quantity > item.stock_quantity && !item.backorders_allowed) {
        throw new SimplerProductOutOfStock();
      }
    });

    return true;
  }

  buildOrderItem(item) {
    return {
      id: item.product_id,
      quantity: parseInt(item.quantity, 10),
      in_stock: item.in_stock,
      backorders_allowed: item.backorders_allowed,
      stock_quantity: item.stock_quantity || 0,
    }
  }
}

class SimplerCheckoutSingle extends SimplerCheckout {
  constructor(href, appId, buttonId) {
    super(href, appId, buttonId);
    this.variationSelected = false;
  }

  buildOrder() {
    const { cart, currency, locale } = this.toJSON();
    const product = cart[0];

    const qtyField = findClosestInput(document.querySelector('.simpler-container'), 'quantity');
    const qty = qtyField !== null ? qtyField.value : 1;
    if (product.product_type === 'simple') {
      this.variationSelected = true;
      return {
        locale,
        currency,
        items: [this.buildOrderItem({ ...product, quantity: qty })]
      };
    }

    const variation = this.getSelectedVariation(product);
    this.variationSelected = Boolean(parseInt(variation.product_id));
    return {
      currency,
      items: [this.buildOrderItem({
        ...product,
        ...variation,
        quantity: qty
      })]
    }
  };

  getSelectedVariation(product) {
    const variationId = findClosestInput(document.querySelector('.simpler-container'), 'variation_id').value;

    if (Boolean(parseInt(variationId))) {
      return product.variations.find(
        el => el.product_id.toString() === variationId.toString()
      ) || {};
    }

    return {};
  }
}

const findClosestInput = (root, inputName) => {
  const cartForm = root.querySelector('form.cart');
  if (cartForm === null) {
    if (root.parentNode === document.body) {
      return null;
    }
    return findClosestInput(root.parentNode, inputName);
  }
  return cartForm.querySelector(`input[name="${inputName}"]`);
}

const openPopup = url => {
  const { left, top } = centerPopup();
  const newWindow = window.open(url, 'popup',
    [
      'scrollbars=yes',
      `width=${SIMPLERWC_POPUP_WIDTH}`,
      `height=${SIMPLERWC_POPUP_HEIGHT}`,
      `top=${top}`,
      `left=${left}`
    ].join(', ')
  );

  if (newWindow.focus) {
    newWindow.focus();
  }
  return newWindow;
}

const centerPopup = () => {
  const dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
  const dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

  const windowWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
  const windowHeight = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

  const left = ((windowWidth / 2) - (SIMPLERWC_POPUP_WIDTH / 2)) + dualScreenLeft;
  const top = ((windowHeight / 2) - (SIMPLERWC_POPUP_HEIGHT / 2)) + dualScreenTop;
  return { left, top };
}

const buildOverlay = popup => {
  const root = document.createElement("div");
  root.classList.add("simpler-overlay");

  const container = document.createElement("div");
  container.classList.add("simpler-overlay-container");

  const closeBtn = document.createElement("span");
  closeBtn.appendChild(document.createTextNode("\u2715"));
  closeBtn.classList.add("simpler-overlay-close");
  closeBtn.addEventListener('click', () => {
    popup.close();
  });

  const lead = document.createElement("object");
  lead.data = "https://cdn.simpler.so/logos/simpler-logo-transparent-white.svg";
  lead.width = "200";
  lead.height = "30";
  lead.classList.add("simpler-overlay-lead");

  const notice = document.createElement("p");
  notice.appendChild(document.createTextNode("Don't see your checkout window?"));
  notice.classList.add("simpler-overlay-notice");

  const cta = document.createElement("a");
  cta.appendChild(document.createTextNode("Click here"));
  cta.href = "#";
  cta.role = "button";
  cta.classList.add("simpler-overlay-cta");
  cta.addEventListener('click', () => {
    popup.focus();
  });

  root.appendChild(container);
  container.appendChild(closeBtn);
  container.appendChild(lead);
  container.appendChild(notice);
  container.appendChild(cta);

  return root;
}

const removeOverlay = overlay => {
  overlay.remove();
  document.body.classList.remove("overlay-active");
}

const handleOrderCreatedMessage = event => {
  try {
    const { action, payload } = JSON.parse(event.data);
    if (action === 'simpler.orderCreated') {
      window.location = window.location.origin + `?simpler_order_created=${payload.orderId}`
    }
  } catch { }
}
