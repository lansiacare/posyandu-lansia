"use client"

import type React from "react"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ArrowLeft, Save } from "lucide-react"

interface ElderlyDataFormProps {
  onBack: () => void
}

export function ElderlyDataForm({ onBack }: ElderlyDataFormProps) {
  const [formData, setFormData] = useState({
    name: "",
    nik: "",
    birthDate: "",
    gender: "",
    bloodType: "",
    bpjsNumber: "",
    address: "",
  })

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }))
  }

  const handleSelectChange = (field: string, value: string) => {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Save elderly data
    alert("Data lansia berhasil disimpan!")
    onBack()
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
            <CardTitle className="text-2xl">Data Lansia</CardTitle>
            <CardDescription>Isi data lansia untuk memudahkan pendaftaran Posyandu</CardDescription>
          </CardHeader>
          <CardContent>
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
                <Label htmlFor="gender">Jenis Kelamin</Label>
                <Select onValueChange={(value) => handleSelectChange("gender", value)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Jenis Kelamin" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="L">Laki-laki</SelectItem>
                    <SelectItem value="P">Perempuan</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-2">
                <Label htmlFor="bloodType">Golongan Darah</Label>
                <Select onValueChange={(value) => handleSelectChange("bloodType", value)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Golongan Darah" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="A">A</SelectItem>
                    <SelectItem value="B">B</SelectItem>
                    <SelectItem value="AB">AB</SelectItem>
                    <SelectItem value="O">O</SelectItem>
                  </SelectContent>
                </Select>
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
                <Save className="mr-2 h-4 w-4" />
                Simpan Data
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
