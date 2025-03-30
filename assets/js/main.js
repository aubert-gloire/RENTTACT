// Load featured properties
document.addEventListener('DOMContentLoaded', function() {
    loadFeaturedProperties();
});

function loadFeaturedProperties() {
    const container = document.getElementById('featured-properties');
    
    // This will be replaced with actual AJAX call to load properties
    // For now, showing sample property cards
    const sampleProperties = [
        {
            title: 'Modern Apartment in Kigali',
            price: '$500/month',
            location: 'Kacyiru, Kigali',
            image: 'assets/images/sample-property.jpg'
        },
        // Add more sample properties here
    ];
    
    sampleProperties.forEach(property => {
        const propertyCard = createPropertyCard(property);
        container.appendChild(propertyCard);
    });
}

function createPropertyCard(property) {
    const col = document.createElement('div');
    col.className = 'col-md-4 mb-4';
    
    col.innerHTML = `
        <div class="card property-card">
            <img src="${property.image}" class="card-img-top" alt="${property.title}">
            <div class="card-body">
                <h5 class="card-title">${property.title}</h5>
                <p class="card-text">
                    <strong>${property.price}</strong><br>
                    ${property.location}
                </p>
                <a href="#" class="btn btn-primary">View Details</a>
            </div>
        </div>
    `;
    
    return col;
}

// Add to favorites functionality
function addToFavorites(propertyId) {
    // Will be implemented with AJAX
    console.log('Adding property to favorites:', propertyId);
}

// Property search functionality
function searchProperties(query) {
    // Will be implemented with AJAX
    console.log('Searching properties:', query);
}
