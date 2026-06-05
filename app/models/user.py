from app import db


class Usuario(db.Model):

    __tablename__ = "usuarios"

    id = db.Column(
        db.Integer,
        primary_key=True
    )

    nombre = db.Column(
        db.String(100),
        nullable=False
    )

    correo = db.Column(
        db.String(100),
        unique=True,
        nullable=False
    )

    password = db.Column(
        db.String(255),
        nullable=False
    )

    telefono = db.Column(
        db.String(20)
    )

    ubicacion = db.Column(
        db.String(100)
    )

    habilidades = db.Column(
        db.Text
    )

    disponibilidad = db.Column(
        db.String(50)
    )

    tipo_usuario = db.Column(
        db.Enum(
            "voluntario",
            "organizacion",
            "admin",
            name="tipo_usuario_enum"
        ),
        nullable=False
    )

    # Relaciones

    voluntariados_publicados = db.relationship(
        "Voluntariado",
        backref="organizacion",
        lazy=True
    )

    postulaciones = db.relationship(
        "Postulacion",
        backref="usuario",
        lazy=True
    )

    certificaciones = db.relationship(
        "Certificacion",
        backref="usuario",
        lazy=True
    )