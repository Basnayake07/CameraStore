function validateForm() {
    let isValid = true;

    const productName = document.getElementById('productname').value.trim();
    const brand = document.getElementById('brand').value.trim();
    const type = document.getElementById('type').value.trim();
    const sku = document.getElementById('sku').value.trim();
    const dateAdded = document.getElementById('dateadded').value; // Ensure this is a datetime-local input for both date and time

    const currentDate = new Date(); // Current date and time
    
    // Reset error messages
    document.getElementById('productNameError').innerText = '';
    document.getElementById('brandError').innerText = '';
    document.getElementById('typeError').innerText = '';
    document.getElementById('skuError').innerText = '';
    
    // Validate Product Name
    if (productName === '') {
        document.getElementById('productNameError').innerText = 'Product Name is required';
        isValid = false;
    }

    // Validate Brand
    if (brand === '') {
        document.getElementById('brandError').innerText = 'Brand is required';
        isValid = false;
    }

    // Validate Type
    if (type === '') {
        document.getElementById('typeError').innerText = 'Type is required';
        isValid = false;
    }

    // Validate SKU
    if (sku === '') {
        document.getElementById('skuError').innerText = 'SKU is required';
        isValid = false;
    }

    return isValid;
}
