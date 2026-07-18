// Datos ilustrativos compartidos del prototipo Enlaza
export const categorias = [
  { id: 'ambiental', nombre: 'Ambiental', icono: 'fa-leaf', color: '#A8C9A1', textoOscuro: true, desc: 'Reforestación y cuido de recursos' },
  { id: 'educativo', nombre: 'Educativo', icono: 'fa-book-open', color: '#E9A227', desc: 'Alfabetización, tutorías y mentoría' },
  { id: 'salud', nombre: 'Salud', icono: 'fa-heart-pulse', color: '#5C8A78', desc: 'Promoción de salud y bienestar' },
  { id: 'comunitario', nombre: 'Comunitario', icono: 'fa-people-group', color: '#0F4C5C', desc: 'Cultura local, educación y ocio comunitario' },
  { id: 'cultural', nombre: 'Cultural', icono: 'fa-masks-theater', color: '#F26B4A', desc: 'Conservación e identidad cultural' },
];

export const oportunidades = [
  { id: 'op-1', tag: 'Ambiental', titulo: 'Jornada de reforestación río San Carlos', org: 'Fundación Verde Norte', fecha: '24 ago 2026', hora: '7:00 a. m. – 12:00 m.', ubicacion: 'San Carlos', cupos: 8, cuposTotal: 20, foto: 'reforestacion', descripcion: 'Sembramos especies nativas en la ribera del río San Carlos junto a la comunidad de Aguas Zarcas, para restaurar el bosque de galería y proteger la fuente de agua.', requisitos: ['Ropa de manga larga y botas cerradas', 'Disponibilidad de 5 horas', 'No se requiere experiencia previa'] },
  { id: 'op-2', tag: 'Educativo', titulo: 'Tutorías de matemáticas para primaria', org: 'Asociación Aprender Juntos', fecha: '2 sept 2026', hora: '2:00 p. m. – 4:00 p. m.', ubicacion: 'Ciudad Quesada', cupos: 3, cuposTotal: 10, foto: 'tutorias', descripcion: 'Acompañamos a niños y niñas de tercer y cuarto grado con refuerzo en matemáticas dos tardes por semana, en la escuela Juan XXIII de Ciudad Quesada.', requisitos: ['Paciencia y gusto por enseñar', 'Compromiso mínimo de 4 semanas', 'Se solicita hoja de delincuencia al día'] },
  { id: 'op-3', tag: 'Salud', titulo: 'Feria de salud comunitaria', org: 'Cruz Roja — sede local', fecha: '14 sept 2026', hora: '8:00 a. m. – 1:00 p. m.', ubicacion: 'Florencia', cupos: 12, cuposTotal: 25, foto: 'salud', descripcion: 'Apoyo logístico en la feria de salud de Florencia: registro de personas, orientación de filas y armado de kits informativos junto al equipo de la Cruz Roja.', requisitos: ['Disponibilidad de un día completo', 'Trato amable con adultos mayores', 'No se requiere formación médica'] },
  { id: 'op-4', tag: 'Ambiental', titulo: 'Limpieza de playa y clasificación de residuos', org: 'Colectivo Costas Limpias', fecha: '5 oct 2026', hora: '6:30 a. m. – 10:00 a. m.', ubicacion: 'Playa Grande, Guanacaste', cupos: 15, cuposTotal: 30, foto: 'playa', descripcion: 'Recolección y clasificación de residuos sólidos en Playa Grande, con registro de datos para el informe anual de basura marina.', requisitos: ['Protector solar y gorra', 'Guantes (los prestamos si no tenés)', 'Buena condición física para caminar en arena'] },
  { id: 'op-5', tag: 'Comunitario', titulo: 'Acompañamiento a personas adultas mayores', org: 'Red de Cuido San Carlos', fecha: '18 sept 2026', hora: '3:00 p. m. – 5:00 p. m.', ubicacion: 'Quesada Centro', cupos: 6, cuposTotal: 12, foto: 'comunitario', descripcion: 'Visitas de acompañamiento y actividades recreativas para personas adultas mayores del centro diurno de Quesada.', requisitos: ['Trato respetuoso y paciente', 'Disponibilidad semanal', 'Se prefiere mayoría de edad'] },
  { id: 'op-6', tag: 'Cultural', titulo: 'Rescate de tradiciones orales boyeras', org: 'Casa de la Cultura Zarcero', fecha: '11 oct 2026', hora: '9:00 a. m. – 12:00 m.', ubicacion: 'Zarcero, Alajuela', cupos: 10, cuposTotal: 15, foto: 'cultural', descripcion: 'Entrevistas y registro audiovisual de boyeros y artesanos locales para el archivo cultural del cantón.', requisitos: ['Interés en historia local', 'No se requiere equipo propio'] },
];

export const organizaciones = [
  { id: 'org-1', nombre: 'Fundación Verde Norte', categoria: 'Ambiental', ubicacion: 'San Carlos, Alajuela', desc: 'Trabajamos en restauración de bosques y educación ambiental en la Zona Norte desde 2011.' },
  { id: 'org-2', nombre: 'Asociación Aprender Juntos', categoria: 'Educativo', ubicacion: 'Ciudad Quesada', desc: 'Programas de refuerzo escolar y mentoría para niñez en riesgo social.' },
  { id: 'org-3', nombre: 'Cruz Roja — sede local', categoria: 'Salud', ubicacion: 'Florencia, San Carlos', desc: 'Atención de emergencias y promoción de salud comunitaria.' },
];
