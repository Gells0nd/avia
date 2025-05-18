document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('faqSearch');
  const accordionItems = document.querySelectorAll('.accordion-item');
  const noResultsMessage = document.getElementById('noResultsMessage');

  searchInput.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase().trim();
    let hasResults = false;

    accordionItems.forEach((item) => {
      const question = item.querySelector('.accordion-button').textContent.toLowerCase();
      const answer = item.querySelector('.accordion-body').textContent.toLowerCase();

      if (question.includes(searchTerm) || answer.includes(searchTerm)) {
        item.style.display = '';
        hasResults = true;
      } else {
        item.style.display = 'none';
      }
    });

    if (noResultsMessage) {
      noResultsMessage.style.display = hasResults ? 'none' : 'block';
    }
  });
});
