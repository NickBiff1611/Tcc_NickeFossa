
document.addEventListener('DOMContentLoaded', function() {
    
    const toggleCadastro = document.getElementById('toggleCadastro');
    const toggleLogin = document.getElementById('toggleLogin');
    const loginContainer = document.querySelector('.login-container');
    const cadastroContainer = document.getElementById('cadastroContainer');

    if (toggleCadastro && toggleLogin) {
        toggleCadastro.addEventListener('click', function(e) {
            e.preventDefault();
            loginContainer.style.display = 'none';
            cadastroContainer.style.display = 'block';
        });

        toggleLogin.addEventListener('click', function(e) {
            e.preventDefault();
            cadastroContainer.style.display = 'none';
            loginContainer.style.display = 'block';
        });
    }

    
    const cadastroForm = document.getElementById('cadastroForm');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const senha = document.getElementById('senhaCadastro').value;
            const confirmarSenha = document.getElementById('confirmarSenha').value;
            
            if (senha !== confirmarSenha) {
                alert('As senhas não coincidem!');
                return;
            }
            
            
            alert('Cadastro realizado com sucesso! Faça login para continuar.');
            cadastroContainer.style.display = 'none';
            loginContainer.style.display = 'block';
            cadastroForm.reset();
        });
    }

    
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            
            
            alert(`Bem-vindo de volta! E-mail: ${email}`);
            window.location.href = 'index.html';
        });
    }

    
    const galeriaImgs = document.querySelectorAll('.galeria img');
    galeriaImgs.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.03)';
            this.style.transition = 'transform 0.3s';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});


const usuarios = [
    {
        email: "cliente@exemplo.com",
        senha: "senha123",
        nome: "Cliente Exemplo"
    }
];


function verificarLogin(email, senha) {
    return usuarios.find(user => user.email === email && user.senha === senha);
}




document.getElementById("formCarro").addEventListener("submit", function(e){
  e.preventDefault();
  const km = document.getElementById("km").value;
  if(km <= 0){
    alert("Informe uma quilometragem válida!");
    return;
  }
  alert("Carro cadastrado com sucesso!");
});



function atualizarHeader() {
  const authSection = document.getElementById('auth-buttons');
  const userMenu = document.getElementById('user-menu');
  
  
  const usuarioLogado = localStorage.getItem('usuario_logado') || sessionStorage.getItem('usuario_logado');
  
  if (usuarioLogado) {
    
    if (authSection) authSection.style.display = 'none';
    if (userMenu) userMenu.style.display = 'block';
    
    try {
      const usuario = JSON.parse(usuarioLogado);
      if (document.getElementById('user-icon')) {
        document.getElementById('user-icon').alt = usuario.nome;
      }
    } catch (e) {
      console.error('Erro ao parsear dados do usuário:', e);
    }
  } else {
    
    if (authSection) {
      authSection.innerHTML = `
        <a href="login.html">Login</a>
        <a href="cadastro.html" style="background-color: #ffcc00; color: #000; padding: 8px 15px; border-radius: 4px; font-weight: bold;">Cadastre-se</a>
      `;
      authSection.style.display = 'flex';
      authSection.style.gap = '15px';
      authSection.style.alignItems = 'center';
    }
    if (userMenu) userMenu.style.display = 'none';
  }
}


function setupLogout() {
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      localStorage.removeItem('usuario_logado');
      sessionStorage.removeItem('usuario_logado');
      window.location.href = 'index.html';
    });
  }
}


document.addEventListener('DOMContentLoaded', function() {
  atualizarHeader();
  setupLogout();
});





function atualizarHeader() {
    const authSection = document.getElementById('auth-buttons');
    const userMenu = document.getElementById('user-menu');
    
    
    const usuarioLogado = localStorage.getItem('usuario_logado') || sessionStorage.getItem('usuario_logado');
    
    if (usuarioLogado) {
      
        if (authSection) authSection.style.display = 'none';
        if (userMenu) userMenu.style.display = 'block';
        
        try {
            const usuario = JSON.parse(usuarioLogado);
            if (document.getElementById('user-icon')) {
                document.getElementById('user-icon').alt = usuario.nome;
            }
        } catch (e) {
            console.error('Erro ao parsear dados do usuário:', e);
        }
    } else {
       
        if (authSection) {
            authSection.innerHTML = `
                <a href="login.html">Login</a>
                <a href="cadastro.html" style="background-color: #ffcc00; color: #000; padding: 8px 15px; border-radius: 4px; font-weight: bold;">Cadastre-se</a>
            `;
            authSection.style.display = 'flex';
            authSection.style.gap = '15px';
            authSection.style.alignItems = 'center';
        }
        if (userMenu) userMenu.style.display = 'none';
    }
}


function setupLogout() {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('usuario_logado');
            sessionStorage.removeItem('usuario_logado');
            window.location.href = 'index.html';
        });
    }
}


function verificarLogin() {
    const usuarioLogado = localStorage.getItem('usuario_logado') || sessionStorage.getItem('usuario_logado');
    if (!usuarioLogado) {
        alert('Você precisa estar logado para acessar esta página!');
        window.location.href = 'login.html';
        return false;
    }
    return true;
}


document.addEventListener('DOMContentLoaded', function() {
    atualizarHeader();
    setupLogout();
    
    
    const paginasProtegidas = ['agendamento.html', 'perfil.html', 'perfil-completo.html'];
    const paginaAtual = window.location.pathname.split('/').pop();
    
    if (paginasProtegidas.includes(paginaAtual)) {
        verificarLogin();
    }
});