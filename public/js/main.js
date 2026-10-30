/**
 * ============================================================================
 * ARCHIVO: public/js/main.js
 * ============================================================================
 * Propósito: Lógica del lado del cliente del sistema OntoByte.
 *            Gestiona la interacción del usuario: navegación móvil,
 *            validación de formularios en tiempo real, indicador de
 *            fortaleza de contraseña, contadores animados, efectos de
 *            scroll y cierre de mensajes flash.
 *
 * Seguridad: Todas las validaciones de cliente son refuerzo de UX;
 *            la validación definitiva se realiza SIEMPRE en el servidor.
 * ============================================================================
 */

/* ----------------------------------------------------------------------------
 * SECCIÓN 1: Utilidades auxiliares
 * ---------------------------------------------------------------------------- */

/**
 * Función helper: selecciona un elemento del DOM.
 * @param {string} selector - Selector CSS del elemento
 * @returns {Element|null} Elemento encontrado o null
 */
const $ = (selector) => document.querySelector(selector);

/**
 * Función helper: selecciona una lista de elementos del DOM.
 * @param {string} selector - Selector CSS de los elementos
 * @returns {NodeList} Lista de elementos encontrados
 */
const $$ = (selector) => document.querySelectorAll(selector);

/**
 * Función helper: muestra un error en un campo del formulario.
 * @param {HTMLElement} field - Elemento del campo con error
 * @param {string} message - Mensaje de error a mostrar
 */
function showFieldError(field, message) {
    /* Obtener el contenedor del grupo del campo */
    const group = field.closest('.form-group');

    if (!group) return;

    /* Marcar el grupo con la clase de error (activa el CSS) */
    group.classList.add('has-error');

    /* Mostrar el mensaje dentro del contenedor de errores del grupo */
    const errorElement = group.querySelector('.form-error');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

/**
 * Función helper: limpia el error mostrado en un campo del formulario.
 * @param {HTMLElement} field - Elemento del campo a limpiar
 */
function clearFieldError(field) {
    /* Obtener el contenedor del grupo del campo */
    const group = field.closest('.form-group');

    if (!group) return;

    /* Quitar la clase de error y limpiar el mensaje */
    group.classList.remove('has-error');

    const errorElement = group.querySelector('.form-error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 2: Barra de navegación (menú móvil y efecto scroll)
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa la navegación del sitio.
 * - Alterna el menú hamburguesa en móvil.
 * - Añade sombra al header al hacer scroll.
 */
function initNavigation() {
    /* --- Botón hamburguesa (toggle del menú móvil) --- */
    const navToggle = $('#navToggle');
    const mainNav   = $('#mainNav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            /* Alternar el estado abierto/cerrado del menú */
            const isOpen = mainNav.classList.toggle('open');

            /* Actualizar accesibilidad: aria-expanded en el botón */
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        /* Cerrar el menú al hacer clic en un enlace de navegación (móvil) */
        mainNav.querySelectorAll('.nav-link, .nav-actions a').forEach((link) => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* --- Efecto de scroll en el header --- */
    const header = $('#siteHeader');

    if (header) {
        window.addEventListener('scroll', () => {
            /* Si hay scroll vertical, agregar clase 'scrolled' */
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 3: Contadores animados de estadísticas
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa los contadores numéricos animados de la sección de métricas.
 * Los número incrementan de forma progresiva hasta su valor objetivo.
 */
function initCounters() {
    const counters = $$('[data-counter]');

    counters.forEach((counter) => {
        /* Valor objetivo que alcanzará el contador */
        const target = parseInt(counter.dataset.counter, 10) || 0;

        /* Duración total de la animación en milisegundos */
        const duration = 1800;

        /* Opciones del observador de intersección */
        const options = { threshold: 0.5 };

        /* Callback ejecutado cuando el elemento entra en el viewport */
        const observerCallback = (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    /* Iniciar la animación del contador */
                    animateCounter(counter, target, duration);
                    /* Dejar de observar el elemento (solo anima una vez) */
                    observer.unobserve(counter);
                }
            });
        };

        /* Crear y activar el observador de intersección */
        const observer = new IntersectionObserver(observerCallback, options);
        observer.observe(counter);
    });
}

/**
 * Anima un contador numérico desde cero hasta su valor objetivo.
 * @param {HTMLElement} element - Elemento que contiene el número
 * @param {number} target - Valor final que debe mostrar
 * @param {number} duration - Duración total de la animación (ms)
 */
function animateCounter(element, target, duration) {
    const startTime = performance.now();

    /* Usar requestAnimationFrame para animación fluida */
    const updateCounter = (currentTime) => {
        /* Calcular el progreso de la animación (0 a 1) */
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        /* Aplicar una curva de easing (desaceleración suave) */
        const eased = 1 - Math.pow(1 - progress, 3);

        /* Actualizar el texto del elemento con el valor redondeado */
        element.textContent = Math.round(eased * target);

        /* Continuar la animación si no ha terminado */
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    };

    requestAnimationFrame(updateCounter);
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 4: Validación de formularios (validaciones en el cliente)
 * ---------------------------------------------------------------------------- */

/**
 * Validadores específicos por tipo de campo.
 * Cada validador devuelve true si es válido, o una cadena con el error.
 */
const FieldValidators = {
    /**
     * Valida un nombre (solo letras, acentos, espacios y puntos).
     * @param {string} value - Valor del campo
     * @returns {boolean|string} true o mensaje de error
     */
    name(value) {
        if (value.trim().length < 2) return 'El nombre debe tener al menos 2 caracteres.';
        if (!/^[a-zA-ZáéíóúñÑüÜÁÉÍÓÚ\s\.]+$/.test(value.trim())) {
            return 'Solo se permiten letras y espacios.';
        }
        return true;
    },

    /**
     * Valida una dirección de email con patrón estricto.
     * @param {string} value - Valor del campo
     * @returns {boolean|string} true o mensaje de error
     */
    email(value) {
        /* Reutilizar la validación nativa de tipo email del navegador */
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value.trim())) {
            return 'Ingresa un email válido (ej: nombre@dominio.com).';
        }
        return true;
    },

    /**
     * Valida un número de teléfono (permite +, números, espacios, guiones).
     * @param {string} value - Valor del campo
     * @returns {boolean|string} true o mensaje de error
     */
    phone(value) {
        /* Campo opcional: si está vacío, es válido */
        if (value.trim() === '') return true;
        if (!/^[+\d\s\-()]{7,20}$/.test(value.trim())) {
            return 'Ingresa un teléfono válido (ej: +57 300 000 0000).';
        }
        return true;
    },

    /**
     * Valida un asunto o título (mínimo de caracteres).
     * @param {string} value - Valor del campo
     * @param {number} min - Longitud mínima
     * @returns {boolean|string} true o mensaje de error
     */
    subject(value, min = 3) {
        if (value.trim().length < min) {
            return `Este campo debe tener al menos ${min} caracteres.`;
        }
        return true;
    },

    /**
     * Valida un mensaje (mínimo de caracteres).
     * @param {string} value - Valor del campo
     * @param {number} min - Longitud mínima
     * @returns {boolean|string} true o mensaje de error
     */
    message(value, min = 10) {
        if (value.trim().length < min) {
            return `El mensaje debe tener al menos ${min} caracteres.`;
        }
        return true;
    },

    /**
     * Valida una contraseña (mínimo 8, mayúscula, minúscula y número).
     * @param {string} value - Valor del campo
     * @returns {boolean|string} true o mensaje de error
     */
    password(value) {
        if (value.length < 8) return 'La contraseña debe tener al menos 8 caracteres.';
        if (!/[A-Z]/.test(value)) return 'Debe incluir al menos una letra mayúscula.';
        if (!/[a-z]/.test(value)) return 'Debe incluir al menos una letra minúscula.';
        if (!/[0-9]/.test(value)) return 'Debe incluir al menos un número.';
        return true;
    },

    /**
     * Valida que dos contraseñas sean idénticas.
     * @param {string} value - Valor del campo de confirmación
     * @param {string} passwordValue - Valor de la contraseña original
     * @returns {boolean|string} true o mensaje de error
     */
    passwordConfirm(value, passwordValue) {
        if (value !== passwordValue) return 'Las contraseñas no coinciden.';
        return true;
    }
};

/**
 * Inicializa la validación en tiempo real y al enviar de un formulario.
 * Procesa cada campo con atributo data-validate definido en el HTML.
 * @param {HTMLFormElement} form - Formulario a validar
 */
function initFormValidation(form) {
    /* Obtener todos los campos marcados para validación de cliente */
    const fields = form.querySelectorAll('input, textarea, select');

    /* Mapa de errores activos del formulario */
    const errors = {};

    /**
     * Ejecuta el validador correcto para cada campo.
     * @param {HTMLInputElement} field - Campo a validar
     * @returns {boolean|string} true o mensaje de error
     */
    const validateField = (field) => {
        const value      = field.value;
        const fieldId    = field.id;
        const validator  = field.dataset.validate;

        /* Si el campo requiere confirmación de contraseña */
        if (field.type === 'password' && field.name === 'password_confirm') {
            const passwordField = form.querySelector('#password');
            return FieldValidators.passwordConfirm(value, passwordField ? passwordField.value : '');
        }

        /* Si el campo tiene un tipo de validador definido */
        if (validator && FieldValidators[validator]) {
            return FieldValidators[validator](value);
        }

        /* Validaciones por tipo nativo de HTML5 */
        if (field.type === 'email') return FieldValidators.email(value);
        if (field.type === 'tel')   return FieldValidators.phone(value);

        /* Suficiente para el resto de campos */
        return true;
    };

    /* --- Validación en vivo: al escribir en cada campo --- */
    fields.forEach((field) => {
        field.addEventListener('input', () => {
            const result = validateField(field);
            if (result === true) {
                clearFieldError(field);
                delete errors[field.name];
            } else {
                showFieldError(field, result);
                errors[field.name] = result;
            }
        });

        /* Limpiar error al enfocar el campo */
        field.addEventListener('focus', () => clearFieldError(field));
    });

    /* --- Validación al enviar el formulario --- */
    form.addEventListener('submit', (event) => {
        let isValid = true;
        Object.keys(errors).forEach((key) => delete errors[key]);

        fields.forEach((field) => {
            /* Validar los campos no opcionales */
            if (field.required || (field.dataset.validate && field.value.trim() !== '')) {
                const result = validateField(field);
                if (result !== true) {
                    showFieldError(field, result);
                    errors[field.name] = result;
                    isValid = false;
                }
            }
        });

        /* Validar casillas de consentimiento (checkbox) */
        const checkboxes = form.querySelectorAll('input[type="checkbox"][required]');
        checkboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                showFieldError(checkbox, 'Debes aceptar este campo para continuar.');
                isValid = false;
            }
        });

        /* Si hay errores, prevenir el envío del formulario */
        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();

            /* Enfocar el primer campo con error para orientar al usuario */
            const firstError = form.querySelector('.has-error input, .has-error textarea');
            if (firstError) {
                firstError.focus();
            }
        }
    });
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 5: Botón de mostrar/ocultar contraseña
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa los botones de visibilidad de contraseña.
 * Permite al usuario mostrar u ocultar su contraseña (UX).
 */
function initPasswordToggles() {
    const toggles = $$('.password-toggle');

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            /* Obtener el campo de contraseña asociado mediante data-target */
            const targetId  = toggle.dataset.target;
            const input     = document.getElementById(targetId);
            const icon      = toggle.querySelector('i');

            /* Alternar el tipo de campo: password <-> text */
            if (input && input.type === 'password') {
                input.type = 'text';
                if (icon) icon.className = 'fas fa-eye-slash';
                toggle.setAttribute('aria-label', 'Ocultar contraseña');
            } else if (input) {
                input.type = 'password';
                if (icon) icon.className = 'fas fa-eye';
                toggle.setAttribute('aria-label', 'Mostrar contraseña');
            }
        });
    });
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 6: Indicador de fortaleza de contraseña
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa el indicador visual de fortaleza de contraseña.
 * Analiza el contenido de la contraseña y actualiza la barra coloreada.
 */
function initPasswordStrength() {
    const passwordInput = $('#registerForm #password');
    const strengthBar   = $('#strengthBar');

    /* Si no existe el formulario de registro, no hacer nada */
    if (!passwordInput || !strengthBar) return;

    passwordInput.addEventListener('input', () => {
        /* Calcular la puntuación de la contraseña (0 a 4) */
        const value = passwordInput.value;
        let score = 0;

        /* Sumar puntos por cada criterio de seguridad cumplido */
        if (value.length >= 8) score++;                    /* Longitud suficiente */
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++; /* Mayúscula + minúscula */
        if (/[0-9]/.test(value)) score++;                  /* Contiene números */
        if (/[^A-Za-z0-9]/.test(value)) score++;           /* Contiene símbolos */

        /* Asignar la clase CSS según la puntuación obtenida */
        strengthBar.className = 'strength-bar';
        if (score <= 1) {
            strengthBar.classList.add('weak');
        } else if (score <= 3) {
            strengthBar.classList.add('moderate');
        } else {
            strengthBar.classList.add('strong');
        }
    });
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 7: Cierre de mensajes flash
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa los botones de cierre de los mensajes flash.
 * Oculta el mensaje con una transición y lo elimina del DOM.
 */
function initFlashClose() {
    const closeButtons = $$('.flash-close');

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            /* Obtener el contenedor del mensaje (padre del botón) */
            const message = button.closest('.flash-message');

            if (!message) return;

            /* Animación de salida suave */
            message.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            message.style.opacity = '0';
            message.style.transform = 'translateY(-8px)';

            /* Eliminar el elemento después de la animación */
            setTimeout(() => message.remove(), 300);
        });
    });
}

/* ----------------------------------------------------------------------------
 * SECCIÓN 8: Inicialización de todas las funcionalidades
 * Ejecuta cada módulo cuando el DOM está completamente cargado.
 * ---------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initCounters();
    initPasswordToggles();
    initPasswordStrength();
    initFlashClose();
    initHeroNetwork();

    /* Inicializar validación de todos los formularios con id específico */
    ['loginForm', 'registerForm', 'contactForm', 'contactFormHome'].forEach((formId) => {
        const form = document.getElementById(formId);
        if (form) {
            initFormValidation(form);
        }
    });
});

/* ----------------------------------------------------------------------------
 * SECCIÓN 9: Fondo interactivo de partículas (red neuronal) en el Hero
 * ---------------------------------------------------------------------------- */

/**
 * Inicializa la red de nodos animados sobre el Hero (canvas #heroNetwork).
 * Canvas 2D nativo, SIN librerías externas, optimizado para no afectar la CPU:
 *   - Se anima SOLO cuando el hero es visible (IntersectionObserver).
 *   - Se pausa cuando la pestaña no es visible (document.visibilitychange).
 *   - Se omite en móviles (< 640px) por CSS y en "prefers-reduced-motion".
 *   - devicePixelRatio limitado a 1.5 y partículas proporcionales al área del hero.
 *   - El canvas tiene pointer-events:none en CSS: NUNCA bloquea botones/enlaces.
 */
function initHeroNetwork() {
    const canvas = $('#heroNetwork');

    /* Si no existe el canvas (páginas sin hero), terminar sin efecto */
    if (!canvas) return;

    /* Respeta la preferencia de reducir movimiento del usuario */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    /* Móviles pequeños: el canvas está oculto por CSS (display:none) */
    if (window.innerWidth < 640) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    /* Configuración del efecto (todos los valores en "const") */
    const LINK_DIST  = 140;                       /* Distancia máxima entre nodos conectados (px) */
    const dpr        = Math.min(window.devicePixelRatio || 1, 1.5); /* Nitidez sin castigar la GPU */
    const COLOR_NODE = 'rgba(16, 185, 129, 0.55)'; /* Verde menta corporativo */
    const COLOR_LINE = 'rgba(16, 185, 129, ';      /* Alfa dinámico según distancia (se completa abajo) */

    let width = 0;
    let height = 0;
    let particles = [];
    let raf = null;                               /* Id del requestAnimationFrame activo */
    let resizeTimer = null;

    /* Redimensiona el canvas al tamaño real del hero (medido a su padre) */
    function resize() {
        const rect = canvas.parentElement.getBoundingClientRect();
        if (!rect || rect.width === 0 || rect.height === 0) return;

        width  = rect.width;
        height = rect.height;

        canvas.width  = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        canvas.style.width  = width + 'px';
        canvas.style.height = height + 'px';

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    /* Genera los nodos: la cantidad se adapta al área del hero (máx 40) */
    function spawn() {
        const count = Math.max(12, Math.min(40, Math.floor((width * height) / 16000)));
        particles = [];
        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.35, /* Velocidad lenta y relajada */
                vy: (Math.random() - 0.5) * 0.35,
                r: Math.random() * 1.8 + 0.9       /* Radio del nodo (1 a 2.7 px) */
            });
        }
    }

    /* Dibuja un frame: nodos + líneas de conexión */
    function step() {
        ctx.clearRect(0, 0, width, height);

        /* 1) Nodos: círculos verdes corporativos */
        for (const p of particles) {
            p.x += p.vx;
            p.y += p.vy;

            /* Rebotar en los bordes del hero */
            if (p.x < 0 || p.x > width)  p.vx *= -1;
            if (p.y < 0 || p.y > height) p.vy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = COLOR_NODE;
            ctx.fill();
        }

        /* 2) Conexiones: línea tenue entre nodos cercanos */
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const a = particles[i];
                const b = particles[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const dist = Math.hypot(dx, dy);

                if (dist < LINK_DIST) {
                    const alpha = 0.22 * (1 - dist / LINK_DIST);
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.strokeStyle = COLOR_LINE + alpha.toFixed(3) + ')';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        raf = requestAnimationFrame(step);
    }

    /* Control del bucle: solo correr cuando el hero es visible */
    function start() {
        if (!raf && width > 0 && height > 0) {
            raf = requestAnimationFrame(step);
        }
    }
    function stop() {
        if (raf) {
            cancelAnimationFrame(raf);
            raf = null;
        }
    }

    /* IntersectionObserver: anima solo cuando el hero entra en el viewport */
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    start();
                } else {
                    stop();
                }
            });
        }, { threshold: 0 });
        observer.observe(canvas);
    }

    /* Pausar al cambiar de pestaña (evita consumo en segundo plano) */
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
        } else {
            start();
        }
    });

    /* Redimensionamiento con debounce (200ms) para no regenerar todo el tiempo */
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            resize();
            spawn();
            stop();
            start();
        }, 200);
    });

    /* Medir y poblar el canvas una sola vez al cargar */
    resize();
    spawn();
}