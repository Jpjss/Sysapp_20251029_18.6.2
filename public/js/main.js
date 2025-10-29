// Funções JavaScript principais do sistema

const $ = window.jQuery // Declare the $ variable before using it

$(document).ready(() => {
  // Auto-hide flash messages após 5 segundos
  setTimeout(() => {
    $(".flash-message").fadeOut("slow")
  }, 5000)

  // Confirmação de exclusão
  $(".btn-danger").on("click", (e) => {
    if (!confirm("Tem certeza que deseja excluir este registro?")) {
      e.preventDefault()
      return false
    }
  })

  // Focus no primeiro campo de formulário
  $("input:text:visible:first").focus()
  
  // Theme toggle
  initThemeToggle()
})

// Gerenciamento de tema claro/escuro
function initThemeToggle() {
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon = document.getElementById('themeIcon');
  const html = document.documentElement;
  
  if (!themeToggle) return;
  
  // Carregar tema salvo
  const savedTheme = localStorage.getItem('theme') || 'dark';
  html.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);
  
  // Evento de clique
  themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
  });
  
  function updateThemeIcon(theme) {
    if (!themeIcon) return;
    
    if (theme === 'dark') {
      // Ícone de lua (modo escuro ativo)
      themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>';
    } else {
      // Ícone de sol (modo claro ativo)
      themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
    }
  }
}

// Função para fazer requisições AJAX
function ajaxRequest(url, data, callback) {
  $.ajax({
    url: url,
    type: "POST",
    data: data,
    dataType: "json",
    success: (response) => {
      if (callback) callback(response)
    },
    error: (xhr, status, error) => {
      console.error("Erro na requisição:", error)
      alert("Erro ao processar requisição")
    },
  })
}
