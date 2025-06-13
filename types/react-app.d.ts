/// <reference types="react" />
/// <reference types="react-dom" />

declare namespace React {
  interface HTMLAttributes<T> {
    // Tambahan untuk atribut HTML yang mungkin digunakan
    [key: string]: any
  }
}
