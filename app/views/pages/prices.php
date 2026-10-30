<?php
/**
 * ============================================================================
 * ARCHIVO: app/views/pages/prices.php
 * ============================================================================
 * Propósito: Página pública de planes y precios de OntoByte.
 *            Presenta los planes de desarrollo, el desglose de servicios
 *            individuales y los módulos adicionales, todos en pesos
 *            colombianos (COP) con IVA incluido.
 * ============================================================================
 */

/* Título dinámico de la página */
$pageTitle = 'Precios';
?>

<!-- ============================================================
     ENCABEZADO INTERNO DE LA PÁGINA
     ============================================================ -->
<section class="page-hero">
    <div class="container">
        <span class="section-eyebrow">Planes y tarifas</span>
        <h1 class="page-hero-title">Precios <span class="text-gradient">claros y sin trampas</span></h1>
        <p class="page-hero-text">
            Conoce cuánto cuesta cada solución de OntoByte, desglosado y en pesos colombianos (COP) con IVA incluido.
            Sin costos ocultos: cotizamos a la medida de tu idea, sin costo.
        </p>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: PLANES DE DESARROLLO
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Nuestros planes</span>
            <h2 class="section-title">Elige el plan según tu <span class="text-gradient">necesidad</span></h2>
        </div>

        <div class="pricing-grid">
            <!-- Plan Básico -->
            <article class="pricing-card">
                <h3 class="pricing-name">Página Básica</h3>
                <p class="pricing-description">Ideal para presentar tu negocio en la web.</p>
                <div class="pricing-price">$650.000</div>
                <div class="pricing-extra">Pago único · Entrega 1 semana</div>
                <ul class="pricing-feats">
                    <li>Diseño 100% a medida (sin plantillas)</li>
                    <li>Hasta 5 secciones</li>
                    <li>Formulario de contacto incluido</li>
                    <li>Adaptable a móvil, tablet y PC</li>
                </ul>
                <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-sm">Solicitar cotización</a>
            </article>

            <!-- Plan Premium (recomendado) -->
            <article class="pricing-card highlight">
                <span class="pricing-badge">MÁS SOLICITADO</span>
                <h3 class="pricing-name">Página Premium</h3>
                <p class="pricing-description">Sitio completo con base de datos y panel de administración.</p>
                <div class="pricing-price">$1.800.000</div>
                <div class="pricing-extra">Pago único · Entrega 2-3 semanas</div>
                <ul class="pricing-feats">
                    <li>Hasta 8 secciones</li>
                    <li>Base de datos integrada</li>
                    <li>Registro e inicio de sesión de usuarios</li>
                    <li>Panel de administración</li>
                    <li>Formulario de contacto incluido</li>
                </ul>
                <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-sm">Solicitar cotización</a>
            </article>

            <!-- Plan Tienda en Línea -->
            <article class="pricing-card">
                <h3 class="pricing-name">Tienda en Línea</h3>
                <p class="pricing-description">Vende tus productos por internet con carrito y pagos.</p>
                <div class="pricing-price">$2.800.000</div>
                <div class="pricing-extra">Pago único · Entrega 4 semanas</div>
                <ul class="pricing-feats">
                    <li>Catálogo de productos y carrito de compras</li>
                    <li>Pasarela de pago</li>
                    <li>Base de datos y panel completo</li>
                    <li>Registro, sesiones y roles de usuarios</li>
                    <li>Panel de administración</li>
                </ul>
                <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-sm">Solicitar cotización</a>
            </article>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: DESGLOSE DE SERVICIOS INDIVIDUALES
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Por servicio</span>
            <h2 class="section-title">Desglose de <span class="text-gradient">servicios</span></h2>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Qué incluye</th>
                        <th>Tiempo</th>
                        <th>Desde</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Diseño Web Creativo</strong></td>
                        <td>Identidad visual e interfaz moderna a la medida de tu marca.</td>
                        <td>1 semana</td>
                        <td class="pricing-value">$400.000</td>
                    </tr>
                    <tr>
                        <td><strong>Desarrollo Web con Base de Datos</strong></td>
                        <td>Sitio completo con base de datos, registro y panel de administración.</td>
                        <td>2-3 semanas</td>
                        <td class="pricing-value">$1.200.000</td>
                    </tr>
                    <tr>
                        <td><strong>Desarrollo Móvil</strong></td>
                        <td>Aplicaciones nativas e híbridas para iOS y Android.</td>
                        <td>4-6 semanas</td>
                        <td class="pricing-value">$3.200.000</td>
                    </tr>
                    <tr>
                        <td><strong>Inteligencia Artificial</strong></td>
                        <td>Chatbots, asistentes virtuales y soluciones de datos a la medida.</td>
                        <td>3-5 semanas</td>
                        <td class="pricing-value">$1.800.000</td>
                    </tr>
                    <tr>
                        <td><strong>Ciberseguridad</strong></td>
                        <td>Auditoría, protección y blindaje de tu sitio frente a ataques.</td>
                        <td>1-2 semanas</td>
                        <td class="pricing-value">$900.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: MÓDULOS ADICIONALES (desglose por módulo)
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">A la medida</span>
            <h2 class="section-title">Módulos <span class="text-gradient">adicionales</span></h2>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Módulo</th>
                        <th>Detalle</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><strong>Sección web adicional</strong></td><td>Cada página o sección extra sobre el plan básico.</td><td class="pricing-value">+$80.000</td></tr>
                    <tr><td><strong>Formulario de contacto</strong></td><td>Envío de mensajes del sitio al correo del cliente.</td><td class="pricing-value">$120.000</td></tr>
                    <tr><td><strong>Registro e inicio de sesión</strong></td><td>Cuentas de usuario con roles y sesiones seguras.</td><td class="pricing-value">+$250.000</td></tr>
                    <tr><td><strong>Módulo de base de datos</strong></td><td>Almacenamiento y gestión de la información del negocio.</td><td class="pricing-value">+$350.000</td></tr>
                    <tr><td><strong>Panel de administración</strong></td><td>Panel personalizado para gestionar todo el contenido.</td><td class="pricing-value">+$600.000</td></tr>
                    <tr><td><strong>Pasarela de pago</strong></td><td>Cobros en línea seguros en tiendas y sistemas.</td><td class="pricing-value">+$400.000</td></tr>
                    <tr><td><strong>Rediseño de página existente</strong></td><td>Renovación visual de un sitio web que ya tienes.</td><td class="pricing-value">desde $300.000</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ============================================================
     SECCIÓN: EXTRAS Y COMPLEMENTOS
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Extras</span>
            <h2 class="section-title">Complementos de <span class="text-gradient">lanzamiento</span></h2>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Complemento</th>
                        <th>Detalle</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><strong>Hosting + Dominio</strong></td><td>Configuración y puesta en línea del sitio en internet durante 1 año.</td><td class="pricing-value">$250.000</td></tr>
                    <tr><td><strong>Mantenimiento mensual</strong></td><td>Actualizaciones, respaldos y monitoreo del sitio.</td><td class="pricing-value">$150.000/mes</td></tr>
                    <tr><td><strong>Bono de soporte remoto</strong></td><td>5 horas de soporte técnico mensual.</td><td class="pricing-value">$200.000</td></tr>
                </tbody>
            </table>
        </div>

        <div class="pricing-note-card">
            <i class="fas fa-shield-alt" aria-hidden="true"></i>
            <p>
                <strong>Todos nuestros proyectos incluyen:</strong> diseño a medida, desarrollo seguro,
                adaptación a móvil y PC, pruebas completas, capacitación de uso y garantía de
                <strong>30 días</strong> de ajustes. Pagas <strong>50% al iniciar y 50% al entregar</strong>.
                Precios en pesos colombianos (COP) con IVA incluido · Cotización exacta sin costo.
            </p>
        </div>

        <div class="text-center" style="margin-top: 2rem;">
            <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane" aria-hidden="true"></i> Cotiza tu proyecto sin costo
            </a>
        </div>
    </div>
</section>