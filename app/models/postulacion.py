from app import db
from datetime import datetime


class Postulacion(db.Model):

    __tablename__ = "postulaciones"

    id = db.Column(
        db.Integer,
        primary_key=True
    )

    fecha_postulacion = db.Column(
        db.DateTime,
        default=datetime.utcnow
    )

    estado = db.Column(
        db.String(30),
        default="pendiente"
    )

    usuario_id = db.Column(
        db.Integer,
        db.ForeignKey("usuarios.id"),
        nullable=False
    )

    voluntariado_id = db.Column(
        db.Integer,
        db.ForeignKey("voluntariados.id"),
        nullable=False
    )