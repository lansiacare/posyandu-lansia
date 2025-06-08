"use client"

import type React from "react"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { ArrowLeft, UserCheck } from "lucide-react"

interface RegistrationFormProps {
  selectedDate: string
  locationName: string
  onBack: () => void
  onSuccess: (queueNumber: number) => void
}

export function RegistrationForm({ selectedDate, locationName, onBack, onSuccess }: RegistrationFormProps) {
  const [formData, setFormData] = useState({
    name: "",
    nik: "",
    birthDate: "",
    bpjsNumber: "",
    address: "",
  })

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }))
  }

  const handleAutofill = () => {
    // Simulate autofill from saved elderly data
    setFormData({
      name: "Siti Aminah",
      nik: "3404012345678901",
      birthDate: "1957-03-15",
      bpjsNumber: "0001234567890",
      address: "Jl. Mawar No. 123, Condongcatur, Depok, Sleman",
    })
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Generate queue number
    const queueNumber = Math.floor(Math.random() * 50) + 1
    onSuccess(queueNumber)
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="max-w-2xl mx-auto p-4">
        <div className="mb-6">
          <Button variant="ghost" className="mb-4" onClick={onBack}>
            <ArrowLeft className="mr-2 h-4 w-4" />
            Kembali
          </Button>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-2xl">Pendaftaran Posyandu</CardTitle>
            <CardDescription>
              Daftar untuk {locationName} pada tanggal{" "}
              {new Date(selectedDate).toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
              })}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="mb-4">
              <Button variant="outline" onClick={handleAutofill} className="w-full">
                <UserCheck className="mr-2 h-4 w-4" />
                Isi Otomatis dari Data Tersimpan
              </Button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Lengkap</Label>
                <Input
                  id="name"
                  name="name"
                  placeholder="Masukkan nama lengkap"
                  value={formData.name}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="nik">NIK</Label>
                <Input
                  id="nik"
                  name="nik"
                  placeholder="Masukkan NIK (16 digit)"
                  value={formData.nik}
                  onChange={handleInputChange}
                  maxLength={16}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="birthDate">Tanggal Lahir</Label>
                <Input
                  id="birthDate"
                  name="birthDate"
                  type="date"
                  value={formData.birthDate}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="bpjsNumber">Nomor BPJS</Label>
                <Input
                  id="bpjsNumber"
                  name="bpjsNumber"
                  placeholder="Masukkan nomor BPJS"
                  value={formData.bpjsNumber}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="address">Alamat Lengkap</Label>
                <Input
                  id="address"
                  name="address"
                  placeholder="Masukkan alamat lengkap"
                  value={formData.address}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <Button type="submit" className="w-full">
                Daftar Sekarang
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
