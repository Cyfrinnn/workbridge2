package com.example.last

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sign_up)

        val applicantButton: Button = findViewById(R.id.btn_applicant)
        val employerButton: Button = findViewById(R.id.btn_employer)
        val signUpButton: Button = findViewById(R.id.btn_signup)
        val loginTextView: TextView = findViewById(R.id.tv_login)

        applicantButton.setOnClickListener {
            // Handle applicant sign-up option
            Toast.makeText(this, "Applicant selected", Toast.LENGTH_SHORT).show()
        }

        employerButton.setOnClickListener {
            // Handle employer sign-up option
            Toast.makeText(this, "Employer selected", Toast.LENGTH_SHORT).show()
        }

        signUpButton.setOnClickListener {
            val fullName: EditText = findViewById(R.id.et_full_name)
            val applicantEmail: EditText = findViewById(R.id.et_applicant_email)
            val employerEmail: EditText = findViewById(R.id.et_employer_email)
            val applicantPassword: EditText = findViewById(R.id.et_applicant_password)
            val employerPassword: EditText = findViewById(R.id.et_employer_password)
            val applicantConfirmPassword: EditText = findViewById(R.id.et_applicant_confirm_password)
            val employerConfirmPassword: EditText = findViewById(R.id.et_employer_confirm_password)

            val isApplicant = applicantButton.isSelected
            val email = if (isApplicant) applicantEmail.text.toString() else employerEmail.text.toString()
            val password = if (isApplicant) applicantPassword.text.toString() else employerPassword.text.toString()
            val confirmPassword = if (isApplicant) applicantConfirmPassword.text.toString() else employerConfirmPassword.text.toString()

            // Handle sign-up logic
            if (password == confirmPassword) {
                Toast.makeText(this, "Sign up successful", Toast.LENGTH_SHORT).show()
            } else {
                Toast.makeText(this, "Passwords do not match", Toast.LENGTH_SHORT).show()
            }
        }

        loginTextView.setOnClickListener {
            // Handle login link click
            Toast.makeText(this, "Redirect to login", Toast.LENGTH_SHORT).show()
        }
    }
}
