from app import db


class Voluntariado(db.Model):

    __tablename__ = "voluntariados"

    id = db.Column(
        db.Integer,
        primary_key=True
    )

    titulo = db.Column(
        db.String(150),
        nullable=False
    )

    descripcion = db.Column(
        db.Text,
        nullable=False
    )

    categoria = db.Column(
        db.String(50),
        nullable=False
    )

    ubicacion = db.Column(
        db.String(100),
        nullable=False
    )

    fecha = db.Column(
        db.Date,
        nullable=False
    )

    cupos = db.Column(
        db.Integer,
        nullable=False
    )

    estado = db.Column(
        db.String(20),
        default="activo"
    )

    organizacion_id = db.Column(
        db.Integer,
        db.ForeignKey("usuarios.id"),
        nullable=False
    )

    postulaciones = db.relationship(
        "Postulacion",
        backref="voluntariado",
        lazy=True
    )