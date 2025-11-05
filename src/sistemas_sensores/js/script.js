/**
 * SISTEMA: Sistema de Gerenciamento de Sensores
 * ARQUIVO: script.js
 * DESCRIÇÃO: JavaScript para interações e validações
 * AUTOR: Junior Developer
 * VERSÃO: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================
    // VALIDAÇÃO DE FORMULÁRIOS
    // =============================================
    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validarFormulario(this)) {
                e.preventDefault();
            }
        });
    });
    
    /**
     * Valida formulário antes do envio
     */
    function validarFormulario(form) {
        const camposObrigatorios = form.querySelectorAll('[required]');
        let valido = true;
        
        camposObrigatorios.forEach(campo => {
            if (!campo.value.trim()) {
                marcarCampoInvalido(campo);
                valido = false;
            } else {
                marcarCampoValido(campo);
            }
        });
        
        return valido;
    }
    
    /**
     * Marca campo como inválido
     */
    function marcarCampoInvalido(campo) {
        campo.classList.add('is-invalid');
        campo.classList.remove('is-valid');
        
        // Adicionar mensagem de erro se não existir
        if (!campo.nextElementSibling || !campo.nextElementSibling.classList.contains('invalid-feedback')) {
            const erro = document.createElement('div');
            erro.className = 'invalid-feedback';
            erro.textContent = 'Este campo é obrigatório.';
            campo.parentNode.appendChild(erro);
        }
    }
    
    /**
     * Marca campo como válido
     */
    function marcarCampoValido(campo) {
        campo.classList.add('is-valid');
        campo.classList.remove('is-invalid');
        
        // Remover mensagem de erro se existir
        const erro = campo.nextElementSibling;
        if (erro && erro.classList.contains('invalid-feedback')) {
            erro.remove();
        }
    }
    
    // =============================================
    // CONFIRMAÇÃO DE EXCLUSÃO
    // =============================================
    
    const linksExclusao = document.querySelectorAll('a[href*="excluir"]');
    linksExclusao.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir este sensor?')) {
                e.preventDefault();
            }
        });
    });
    
    // =============================================
    // MASCARAS E FORMATAÇÕES
    // =============================================
    
    // Formatação de números decimais
    const camposDecimal = document.querySelectorAll('input[type="number"][step="0.01"]');
    camposDecimal.forEach(campo => {
        campo.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    // =============================================
    // AUTO-FECHAMENTO DE ALERTAS
    // =============================================
    
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alerta => {
        setTimeout(() => {
            if (alerta) {
                const bsAlert = new bootstrap.Alert(alerta);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // =============================================
    // TOOLTIPS
    // =============================================
    
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // =============================================
    // FUNÇÕES UTILITÁRIAS
    // =============================================
    
    /**
     * Formata data para exibição
     */
    window.formatarData = function(data) {
        if (!data) return 'N/A';
        return new Date(data).toLocaleDateString('pt-BR');
    };
    
    /**
     * Formata número com separadores
     */
    window.formatarNumero = function(numero, casasDecimais = 2) {
        if (!numero) return '0';
        return parseFloat(numero).toFixed(casasDecimais).replace('.', ',');
    };
    
});