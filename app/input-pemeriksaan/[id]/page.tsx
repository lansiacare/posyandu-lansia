"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { ArrowLeft, Save, User, Stethoscope } from "lucide-react"
import Link from "next/link"
import { useRouter } from "next/navigation"

interface PatientData {
  id: number
  name: string
  gender: string
  age: number
  birthDate: string
  nik: string
  bpjs: string
  address: string
  bloodType: string
  queueNumber: number
}

interface ExaminationData {
  bloodSugar: string
  systolic: string
  diastolic: string
  cholesterol: string
  weight: string
  height: string
  uricAcid: string
  notes: string
}

export default function InputPemeriksaanPage({ params }: { params: { id: string } }) {
  const [patient, setPatient] = useState<PatientData | null>(null)
  const [examination, setExamination] = useState<ExaminationData>({
    bloodSugar: "",
    systolic: "",
    diastolic: "",
    cholesterol: "",
    weight: "",
    height: "",
    uricAcid: "",
    notes: "",
  })
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState("")
  const [success, setSuccess] = useState("")
  const router = useRouter()

  useEffect(() => {
    // Check if user is kader
    const userData = localStorage.getItem("user")
    if (!userData) {
      router.push("/login")
      return
    }

    const user = JSON.parse(userData)
    if (user.role !== "kader") {
      router.push("/")
      return
    }

    // Load patient data based on ID
    const patientId = params.id

    // In a real app, this would be an API call
    // For demo, we'll use sample data
    const samplePatients: Record<string, PatientData> = {
      "1": {
        id: 1,
        name: "Siti Aminah",
        gender: "P",
        age: 67,
        birthDate: "1957-03-15",
        nik: "3404012345678901",
        bpjs: "0001234567890",
        address: "Jl. Mawar No. 123, Condongcatur, Depok, Sleman",
        bloodType: "A",
        queueNumber: 1,
      },
      "2": {
        id: 2,
        name: "Budi Santoso",
        gender: "L",
        age: 72,
        birthDate: "1952-08-20",
        nik: "3404012345678902",
        bpjs: "0001234567891",
        address: "Jl. Melati No. 456, Caturtunggal, Depok, Sleman",
        bloodType: "B",
        queueNumber: 2,
      },
      "3": {
        id: 3,
        name: "Mariam Sari",
        gender: "P",
        age: 69,
        birthDate: "1955-12-10",
        nik: "3404012345678903",
        bpjs: "0001234567892",
        address: "Jl. Anggrek No. 789, Maguwoharjo, Depok, Sleman",
        bloodType: "O",
        queueNumber: 3,
      },
      "4": {
        id: 4,
        name: "Ahmad Wijaya",
        gender: "L",
        age: 75,
        birthDate: "1949-05-22",
        nik: "3404012345678904",
        bpjs: "0001234567893",
        address: "Jl. Kaliurang KM 8, Condongcatur, Depok, Sleman",
        bloodType: "AB",
        queueNumber: 4,
      },
      "5": {
        id: 5,
        name: "Ratna Dewi",
        gender: "P",
        age: 68,
        birthDate: "1956-09-30",
        nik: "3404012345678905",
        bpjs: "0001234567894",
        address: "Jl. Babarsari No. 101, Caturtunggal, Depok, Sleman",
        bloodType: "A",
        queueNumber: 5,
      },
    }

    const patientData = samplePatients[patientId]
    if (patientData) {
      setPatient(patientData)

      // Load existing examination data if any
      const existingExam = localStorage.getItem(`examination_${patientId}`)
      if (existingExam) {
        setExamination(JSON.parse(existingExam))
      }
    } else {
      setError("Data pasien tidak ditemukan")
    }
  }, [params.id, router])

  const handleInputChange = (field: keyof ExaminationData, value: string) => {
    setExamination((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsLoading(true)
    setError("")

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 1000))

      // Save examination data to localStorage (in real app, this would be saved to database)
      localStorage.setItem(`examination_${params.id}`, JSON.stringify(examination))

      setSuccess("Data pemeriksaan berhasil disimpan!")

      // Redirect back to kader dashboard after a delay
      setTimeout(() => {
        router.push("/kader-dashboard")
      }, 2000)
    } catch (error) {
      setError("Terjadi kesalahan saat menyimpan data.")
    } finally {
      setIsLoading(false)
    }
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("id-ID", {
      year: "numeric",
      month: "long",
      day: "numeric",
    })
  }

  if (!patient) {
    return (
      <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
          <p>Memuat data pasien...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="max-w-6xl mx-auto p-4">
        <div className="mb-6">
          <Link href="/kader-dashboard">
            <Button variant="outline">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Kembali ke Dashboard
            </Button>
          </Link>
        </div>

        {success && (
          <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{success}</div>
        )}

        {error && <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{error}</div>}

        <div className="grid md:grid-cols-2 gap-6">
          {/* Patient Information */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <User className="mr-2 h-5 w-5 text-blue-600" />
                Informasi Pasien
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <span className="text-sm text-gray-600">Nama:</span>
                  <p className="font-medium">{patient.name}</p>
                </div>
                <div>
                  <span className="text-sm text-gray-600">Jenis Kelamin:</span>
                  <p className="font-medium">{patient.gender === "L" ? "Laki-laki" : "Perempuan"}</p>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <span className="text-sm text-gray-600">Umur:</span>
                  <p className="font-medium">{patient.age} tahun</p>
                </div>
                <div>
                  <span className="text-sm text-gray-600">Tanggal Lahir:</span>
                  <p className="font-medium">{formatDate(patient.birthDate)}</p>
                </div>
              </div>

              <div>
                <span className="text-sm text-gray-600">NIK:</span>
                <p className="font-medium">{patient.nik}</p>
              </div>

              <div>
                <span className="text-sm text-gray-600">BPJS:</span>
                <p className="font-medium">{patient.bpjs}</p>
              </div>

              <div>
                <span className="text-sm text-gray-600">Golongan Darah:</span>
                <p className="font-medium">{patient.bloodType}</p>
              </div>

              <div>
                <span className="text-sm text-gray-600">Alamat:</span>
                <p className="font-medium">{patient.address}</p>
              </div>

              <div className="pt-2 border-t">
                <span className="text-sm text-gray-600">Nomor Antrian:</span>
                <span className="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded font-medium">
                  {patient.queueNumber}
                </span>
              </div>
            </CardContent>
          </Card>

          {/* Examination Form */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center">
                <Stethoscope className="mr-2 h-5 w-5 text-green-600" />
                Data Pemeriksaan
              </CardTitle>
              <CardDescription>Isi data hasil pemeriksaan kesehatan</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="bloodSugar">Gula Darah (mg/dL)</Label>
                    <Input
                      id="bloodSugar"
                      type="number"
                      step="0.01"
                      placeholder="80-120"
                      value={examination.bloodSugar}
                      onChange={(e) => handleInputChange("bloodSugar", e.target.value)}
                    />
                  </div>

                  <div>
                    <Label>Tensi (mmHg)</Label>
                    <div className="flex space-x-2">
                      <Input
                        placeholder="Sistol"
                        type="number"
                        value={examination.systolic}
                        onChange={(e) => handleInputChange("systolic", e.target.value)}
                      />
                      <span className="self-center">/</span>
                      <Input
                        placeholder="Diastol"
                        type="number"
                        value={examination.diastolic}
                        onChange={(e) => handleInputChange("diastolic", e.target.value)}
                      />
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="cholesterol">Kolesterol (mg/dL)</Label>
                    <Input
                      id="cholesterol"
                      type="number"
                      step="0.01"
                      placeholder="< 200"
                      value={examination.cholesterol}
                      onChange={(e) => handleInputChange("cholesterol", e.target.value)}
                    />
                  </div>

                  <div>
                    <Label htmlFor="uricAcid">Asam Urat (mg/dL)</Label>
                    <Input
                      id="uricAcid"
                      type="number"
                      step="0.01"
                      placeholder="3.5-7.0"
                      value={examination.uricAcid}
                      onChange={(e) => handleInputChange("uricAcid", e.target.value)}
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="weight">Berat Badan (kg)</Label>
                    <Input
                      id="weight"
                      type="number"
                      step="0.1"
                      placeholder="50.0"
                      value={examination.weight}
                      onChange={(e) => handleInputChange("weight", e.target.value)}
                    />
                  </div>

                  <div>
                    <Label htmlFor="height">Tinggi Badan (cm)</Label>
                    <Input
                      id="height"
                      type="number"
                      step="0.1"
                      placeholder="160.0"
                      value={examination.height}
                      onChange={(e) => handleInputChange("height", e.target.value)}
                    />
                  </div>
                </div>

                <div>
                  <Label htmlFor="notes">Catatan Tambahan</Label>
                  <Textarea
                    id="notes"
                    rows={3}
                    placeholder="Catatan pemeriksaan, keluhan, atau rekomendasi..."
                    value={examination.notes}
                    onChange={(e) => handleInputChange("notes", e.target.value)}
                  />
                </div>

                <Button type="submit" className="w-full" disabled={isLoading}>
                  <Save className="mr-2 h-4 w-4" />
                  {isLoading ? "Menyimpan..." : "Simpan Data Pemeriksaan"}
                </Button>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
