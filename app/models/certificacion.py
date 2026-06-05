from app import db
from datetime import datetime


class Certificacion(db.Model):

    __tablename__ = "certificaciones"

    id = db.Column(
        db.Integer,
        primary_key=True
    )

    fecha_emision = db.Column(
        db.DateTime,
        default=datetime.utcnow
    )

    horas_realizadas = db.Column(
        db.Integer,
        nullable=False
    )

    descripcion = db.Column(
        db.String(255)
    )

    usuario_id = db.Column(
        db.Integer,
        db.ForeignKey("usuarios.id"),
        nullable=False
    )