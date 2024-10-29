document.addEventListener('DOMContentLoaded', function() {
    if(document.querySelectorAll('.global-tab input[type="hidden"]').length > 0 ){
        let tabIndex = document.querySelectorAll('.global-tab input[type="hidden"]').length > 0 
        ? parseInt(document.querySelector('.global-tab:last-child input[type="hidden"]').value) + 1 
        : 0;

        document.getElementById('add-tab').addEventListener('click', function() {
        const container = document.getElementById('global-tabs-container');
        const newTab = document.createElement('div');
        newTab.classList.add('global-tab');
        newTab.innerHTML = `
            <input type="hidden" name="azwctabs_tab_index[${tabIndex}]" value="${tabIndex}"/>
            <input type="text" name="azwctabs_global_tabs[${tabIndex}][title]" placeholder="Tab Title" style="width: 30%;">
            <textarea name="azwctabs_global_tabs[${tabIndex}][content]" placeholder="Tab Content" rows="4" style="width: 65%;"></textarea>
            <button type="button" class="remove-tab button">Remove</button>`;
        container.appendChild(newTab);
        tabIndex++; // Increment index after adding the new tab
        });

        document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tab')) {
            e.target.closest('.global-tab').remove(); // Remove the specific tab
        }
        });
    }


    if(document.querySelectorAll('.product-tab input[type="hidden"]').length > 0 ){

        let productTabIndex = document.querySelectorAll('.product-tab input[type="hidden"]').length > 0 
        ? parseInt(document.querySelector('.product-tab:last-child input[type="hidden"]').value) + 1 
        : 0;
        document.getElementById('add-product-tab').addEventListener('click', function() {
            const container = document.getElementById('product-tabs-container');
            const newTab = document.createElement('div');
            newTab.classList.add('product-tab');
            newTab.innerHTML = `
                <input type="hidden" name="azwctabs_tab_index[${productTabIndex}]" value="${productTabIndex}"/>
                <input type="text" name="azwctabs_product_tabs[${productTabIndex}][title]" placeholder="Tab Title" style="width: 30%;">
                <textarea name="azwctabs_product_tabs[${productTabIndex}][content]" placeholder="Tab Content" rows="4" style="width: 65%;"></textarea>
                <button type="button" class="remove-tab button">Remove</button>`;
            container.appendChild(newTab);
            productTabIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-tab')) {
                e.target.closest('.product-tab').remove();
            }
        });
    }
});