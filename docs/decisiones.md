# Registro de Decisiones de Arquitectura y Diseño

Este documento registra las elecciones técnicas y de alcance acordadas para el **Sistema de Inventario de Componentes de PC**, siguiendo la metodología de la guía del proyecto.

---

## Decisión: Stack de Presentación (Frontend)
- **Fecha:** 4 de noviembre de 2026
- **Opción elegida:** Laravel Clásico (Blade + Controladores tradicionales + Tailwind CSS + Alpine.js).
- **Motivo:** Se descarta Livewire para evitar reactividad compleja en segundo plano y mantener un modelo MVC predecible, directo y fácil de depurar con formularios web estándar.
- **Efecto en el alcance:** Ninguno en las reglas de negocio. Todas las validaciones, políticas, transacciones y pruebas se mantienen intactas.
- **Requiere acuerdo con quien solicita:** Sí (registrado como cambio justificado de la propuesta base de Livewire).

---

## Decisión: Suite de Pruebas Automatizadas
- **Fecha:** 4 de noviembre de 2026
- **Opción elegida:** PHPUnit puro.
- **Motivo:** Se descarta Pest para utilizar las clases y métodos estándar de PHPUnit (`TestCase`, `test_*`, `assert*`), manteniendo el código de pruebas en PHP nativo clásico sin abstracciones adicionales.
- **Efecto en el alcance:** Ninguno. Las pruebas se ejecutan mediante `php artisan test` o `./vendor/bin/phpunit`.
- **Requiere acuerdo con quien solicita:** No (es una de las dos opciones autorizadas en la guía).

---

## Decisión: Entorno y Despliegue
- **Fecha:** 4 de noviembre de 2026
- **Opción elegida:** Entorno 100% Local (macOS / Localhost).
- **Motivo:** Concentrarse exclusivamente en la lógica de negocio, pruebas y funcionamiento antes de contemplar infraestructura en la nube.
- **Efecto en el alcance:** Se omiten configuraciones de servidores remotos; todo se ejecuta y valida localmente.
- **Requiere acuerdo con quien solicita:** No.

---

## Decisión: Alcance de la Versión 1 (V1)
- **Fecha:** 4 de noviembre de 2026
- **Opción elegida:** Control por cantidades de SKU en un solo almacén.
- **Motivo:** Blindar la complejidad inicial para asegurar una entrega funcional completa antes de añadir números de serie individuales, compatibilidad técnica o ensamble de PCs completas.
- **Efecto en el alcance:** Se postergan números de serie (S/N) y garantías para la Versión 2.
- **Requiere acuerdo con quien solicita:** Sí (acordado según la Sección 2 de la guía).
